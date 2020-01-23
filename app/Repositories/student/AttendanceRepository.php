<?php
namespace App\Repositories\Student;

use Carbon\Carbon;
use App\Jobs\SendCustomizedSMS;
use App\Models\Calendar\Holiday;
use App\Models\Academic\Timetable;
use App\Models\Academic\ClassTeacher;
use App\Models\Student\StudentRecord;
use App\Models\Student\StudentAttendance;
use App\Repositories\Academic\BatchRepository;
use Illuminate\Validation\ValidationException;
use App\Repositories\Academic\SubjectRepository;
use App\Repositories\Configuration\Academic\CourseGroupRepository;

class AttendanceRepository
{
    protected $student_record;
    protected $course_group;
    protected $holiday;
    protected $student_attendance;
    protected $batch;
    protected $subject;
    protected $class_teacher;
    protected $timetable;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        StudentRecord $student_record,
        CourseGroupRepository $course_group,
        Holiday $holiday,
        StudentAttendance $student_attendance,
        BatchRepository $batch,
        SubjectRepository $subject,
        ClassTeacher $class_teacher,
        Timetable $timetable
    ) {
        $this->student_record = $student_record;
        $this->course_group = $course_group;
        $this->holiday = $holiday;
        $this->student_attendance = $student_attendance;
        $this->batch = $batch;
        $this->subject = $subject;
        $this->class_teacher = $class_teacher;
        $this->timetable = $timetable;
    }

    /**
     * Get attendance pre requisite.
     *
     * @return Array
     */
    public function getPreRequisite()
    {
        $batches = $this->course_group->getBatchOption();
        $batch_with_subjects = $this->batch->getAllBatchWithSubjects();

        $holidays = $this->holiday->filterBySession()->get();

        $attendance_methods = getStudentAttendanceMethods();

        $attendance_method_more_than_once_types = getStudentAttendanceMoreThanOnceTypes();

        return compact('batches', 'holidays','attendance_methods','batch_with_subjects','attendance_method_more_than_once_types');
    }

    /**
     * Fetch student record and their attendance.
     *
     * @return Array
     */
    public function fetch($params)
    {
        $batch_id = gv($params, 'batch_id');
        $date = toDate(gv($params, 'date_of_attendance'));

        $query = $this->student_record->with('student', 'student.parent', 'admission','batch')->filterBySession()->filterByBatchId($batch_id);

        if (\Auth::user()->hasRole(config('system.default_role.student'))) {
            $query->whereHas('student', function($q) {
                $q->whereId(\Auth::user()->Student->id);
            });
        }

        if (\Auth::user()->hasRole(config('system.default_role.parent'))) {
            $query->whereHas('student', function($q) {
                $q->whereIn('id', \Auth::user()->Parent->Students->pluck('id')->all());
            });
        }

        $student_records = $query->get();

        if (! $batch_id) {
            throw ValidationException::withMessages(['message' => trans('academic.could_not_find_batch')]);
        }

        $batch = $this->batch->findOrFail($batch_id);
        $course = $batch->Course;
        $subjects = $batch->subjects->pluck('id')->all();

        $date = ($date) ? toDate($date) : date('Y-m-d');
        $days = Carbon::parse($date)->daysInMonth;

        $this->validateAttendance($subjects, $params);

        $start_date = date('Y-m', strtotime($date)).'-01';
        $end_date = date('Y-m', strtotime($date)).'-'.$days;

        $class_teachers = $this->getClassTeachers($batch_id);

        $params['start_date'] = $start_date;
        $params['end_date'] = $end_date;
        $attendances = $this->getAttendances($params);

        $attendance = $this->student_attendance->whereBatchId($batch_id)->whereDateOfAttendance($date)->whereSubjectId(gv($params, 'subject_id'))->whereSession(gv($params, 'session'))->first();

        return compact('student_records', 'batch', 'attendances','course','class_teachers','attendance');
    }

    /**
     * Get attendances
     * @param  array  $params
     * @return Array
     */
    private function getAttendances($params = array())
    {
        $query = $this->student_attendance->filterbyBatchId(gv($params, 'batch_id'))->dateOfAttendanceBetween([
            'start_date' => gv($params, 'start_date'),
            'end_date' => gv($params, 'end_date')
        ]);

        $attendance_method = gv($params, 'attendance_method', 'once');

        if ($attendance_method == 'once') {
            $query->whereNull('subject_id')->whereNull('session');
        } else if ($attendance_method == 'more_than_once') {
            $query->whereNull('subject_id')->whereSession(gv($params, 'session'));
        } else if ($attendance_method == 'subject_wise') {
            $query->whereSubjectId(gv($params, 'subject_id'))->whereNull('session');
        }

        return $query->get();
    }

    /**
     * Validate attendance
     * @param  array $subjects
     * @param  array $params
     * @return void
     */
    private function validateAttendance($subjects, $params = array())
    {
        $attendance_method = gv($params, 'attendance_method', 'once');
        $batch_id = gv($params, 'batch_id');
        $date = toDate(gv($params, 'date_of_attendance', date('Y-m-d')));

        if (! in_array($attendance_method, ['once', 'more_than_once', 'subject_wise'])) {
            throw ValidationException::withMessages(['message' => trans('validation.exists', ['attribute' => trans('student.attendance_method')])]);
        }

        $subject_id = gv($params, 'subject_id');
        $session = gv($params, 'session');

        if ($attendance_method == 'subject_wise' && ! in_array($subject_id, $subjects)) {
            throw ValidationException::withMessages(['message' => trans('academic.could_not_find_subject')]);
        }

        if ($attendance_method == 'more_than_once' && ! in_array($session, [1,2,3,4,5])) {
            throw ValidationException::withMessages(['message' => trans('validation.exists', ['attribute' => trans('student.attendance_session')])]);
        }

        $attendance = $this->student_attendance->filterByBatchId($batch_id)->filterByDateOfAttendance($date, 1)->first();

        if (! $attendance) {
            return;
        }

        if ($attendance_method == 'once' && $attendance->subject_id) {
            throw ValidationException::withMessages(['message' => trans('student.attendance_method_subject_wise_marked')]);
        }

        if ($attendance_method == 'once' && $attendance->session) {
            throw ValidationException::withMessages(['message' => trans('student.attendance_method_more_than_once_marked')]);
        }

        if ($attendance_method == 'subject_wise' && $attendance->session) {
            throw ValidationException::withMessages(['message' => trans('student.attendance_method_more_than_once_marked')]);
        }

        if ($attendance_method == 'subject_wise' && ! $attendance->subject_id) {
            throw ValidationException::withMessages(['message' => trans('student.attendance_method_once_marked')]);
        }

        if ($attendance_method == 'more_than_once' && $attendance->subject_id) {
            throw ValidationException::withMessages(['message' => trans('student.attendance_method_subject_wise_marked')]);
        }

        if ($attendance_method == 'more_than_once' && ! $attendance->session) {
            throw ValidationException::withMessages(['message' => trans('student.attendance_method_once_marked')]);
        }
    }

    /**
     * Get class teachers for attendance
     * @param  integer $batch_id
     * @return ClassTeacher
     */
    private function getClassTeachers($batch_id)
    {
        $class_teachers = $this->class_teacher->filterByBatchId($batch_id)->orderBy('date_effective','desc')->get(['employee_id','date_effective']);

        $employee_id = optional(\Auth::user()->Employee)->id;
        foreach ($class_teachers as $class_teacher) {
            $class_teacher->is_me = ($class_teacher->employee_id == $employee_id) ? true : false;
        }

        return $class_teachers;
    }

    /**
     * Store student attendance.
     *
     * @return Array
     */
    public function store($params = array())
    {
        $batch_id = gv($params, 'batch_id');
        $date_of_attendance = toDate(gv($params, 'date_of_attendance', date('Y-m-d')));
        $attendance_method = gv($params, 'attendance_method', 'once');
        $is_default = gbv($params, 'is_default');

        if (! $batch_id) {
            throw ValidationException::withMessages(['message' => trans('academic.could_not_find_batch')]);
        }

        $batch = $this->batch->findOrFail($batch_id);
        $subjects = $batch->subjects->pluck('id')->all();

        $this->validateAttendance($subjects, $params);

        $class_teachers = $this->getClassTeachers($batch_id);

        $auth_user = \Auth::user();

        if (! $auth_user->can('mark-student-attendance') && $auth_user->can('mark-class-teacher-wise-student-attendance') && ! amIClassTeacherOnDate($class_teachers, $date_of_attendance)) {
            throw ValidationException::withMessages(['message' => trans('general.permission_denied')]);
        }

        if (! dateBetweenSession($date_of_attendance)) {
            throw ValidationException::withMessages(['message' => trans('academic.invalid_session_date_range')]);
        }

        $student_records = $this->student_record->filterBySession()->filterbyBatchId($batch_id)->where('date_of_entry','<=',$date_of_attendance)->where(function($q) use($date_of_attendance) {
            $q->where('date_of_exit',null)->orWhere(function($q1) use($date_of_attendance) {
                $q1->where('date_of_exit','!=',null)->where('date_of_exit','>=',$date_of_attendance);
            });
        })->get();

        $subject_id = gv($params, 'subject_id');
        $session = gv($params, 'session');

        if (! config('config.allow_to_modify_student_attendance') && $date_of_attendance < date('Y-m-d')) {
            throw ValidationException::withMessages(['message' => trans('student.cannot_modify_attendance_of_previous_dates')]);
        }

        if (config('config.allow_to_modify_student_attendance') && $date_of_attendance < date('Y-m-d') && dateDiff(date('Y-m-d'), $date_of_attendance) > config('config.days_allowed_to_modify_student_attendance')) {
            throw ValidationException::withMessages(['message' => trans('student.can_mark_attendance_of_days', ['day' => config('config.days_allowed_to_modify_student_attendance')] )]);
        }

        if (! config('config.allow_to_mark_student_advance_attendance') && $date_of_attendance > date('Y-m-d')) {
            throw ValidationException::withMessages(['message' => trans('student.cannot_mark_attendance_of_advance_dates')]);
        }

        if (config('config.allow_to_mark_student_advance_attendance') && $date_of_attendance > date('Y-m-d') && dateDiff(date('Y-m-d'), $date_of_attendance) > config('config.days_allowed_to_mark_student_advance_attendance')) {
            throw ValidationException::withMessages(['message' => trans('student.can_mark_advance_attendance_of_days', ['day' => config('config.days_allowed_to_mark_student_advance_attendance')] )]);
        }

        $students = gv($params, 'students', []);

        if (! $students) {
            throw ValidationException::withMessages(['message' => trans('student.could_not_find')]);
        }

        $student_record_ids = $student_records->pluck('id')->all();
        foreach ($students as $student) {
            $status = gv($student, 'attendance');
            
            if (! in_array(gv($student, 'id'), $student_record_ids) && $status != 'unavailable') {
                throw ValidationException::withMessages(['message' => trans('student.invalid_student_on_date', ['date' => showDate($date_of_attendance), 'name' => gv($student, 'name').' ('.gv($student, 'roll_number').')' ] )]);
            }
        }

        $attendances = array();
        foreach ($students as $student) {
            $id     = gv($student, 'id');
            $status = gv($student, 'attendance');

            if (($status == 'absent' || $status == 'unmarked') && $status != 'unavailable') {
                $attendances[] = array('id' => $id);
            }

            if ($attendance_method != 'once' && $status == 'half_day') {
                throw ValidationException::withMessages(['message' => trans('student.half_day_supported_only_attendance_method_once')]);
            }
        }

        $attendance['data'] = $attendances;
        
        $attendances = array();
        foreach ($students as $student) {
            $id     = gv($student, 'id');
            $status = gv($student, 'attendance');

            if ($status == 'late') {
                $attendances[] = array('id' => $id);
            }
        }

        $attendance['late'] = $attendances;

        $attendances = array();
        foreach ($students as $student) {
            $id     = gv($student, 'id');
            $status = gv($student, 'attendance');

            if ($status == 'half_day') {
                $attendances[] = array('id' => $id);
            }
        }

        $attendance['half_day'] = $attendances;

        $student_attendance = $this->student_attendance->firstOrCreate([
            'date_of_attendance' => toDate($date_of_attendance),
            'batch_id' => $batch_id,
            'subject_id' => $attendance_method == 'subject_wise' ? $subject_id : null,
            'session' => $attendance_method == 'more_than_once' ? $session : null
        ]);

        $student_attendance->attendance = $attendance;
        $student_attendance->save();

        $this->makeDefault($student_attendance, $params);
    }

    /**
     * Make attendance default for day
     * @param  StudentAttendance $student_attendance
     * @param  array $params             
     * @return void
     */
    private function makeDefault($student_attendance, $params = array())
    {
        $date_of_attendance = toDate(gv($params, 'date_of_attendance'));
        $batch_id = gv($params, 'batch_id');

        if (gbv($params, 'is_default')) {
            $this->student_attendance->whereDateOfAttendance($date_of_attendance)->whereBatchId($batch_id)->update(['is_default' => 0]);
            $student_attendance->is_default = 1;
            $student_attendance->save();
            return;
        }
        
        $attendance_count = $this->student_attendance->whereDateOfAttendance($date_of_attendance)->whereBatchId($batch_id)->count();

        if ($attendance_count === 1) {
            $student_attendance->is_default = 1;
            $student_attendance->save();
        }
    }

    public function default($params = array())
    {
        $batch_id = gv($params, 'batch_id');
        $date_of_attendance = toDate(gv($params, 'date_of_attendance'));
        $subject_id = gv($params, 'subject_id');
        $session = gv($params, 'session');

        $student_attendance = $this->student_attendance->whereBatchId($batch_id)->whereDateOfAttendance($date_of_attendance)->whereSubjectId($subject_id)->whereSession($session)->first();

        if (! $student_attendance) {
            throw ValidationException::withMessages(['message' => trans('general.invalid_action')]);
        }

        $params['is_default'] = 1;

        $this->makeDefault($student_attendance, $params);
    }

    /**
     * Get student absentee filters.
     *
     * @return Array
     */
    public function getAbsenteeFilters()
    {
        $batches = $this->course_group->getBatchOption();
        $batch_with_subjects = $this->batch->getAllBatchWithSubjects();
        $attendance_methods = getStudentAttendanceMethods();

        $attendance_method_more_than_once_types = getStudentAttendanceMoreThanOnceTypes();

        return compact('batches','batch_with_subjects','attendance_methods','attendance_method_more_than_once_types');
    }

    /**
     * Get absentee list
     * @param  array $params
     * @return StudentRecord
     */
    public function getAbsenteeData($params = array())
    {
        $date                 = toDate(gv($params, 'date', date('Y-m-d')));
        $batch_id             = gv($params, 'batch_id');
        $first_name           = gv($params, 'first_name');
        $last_name            = gv($params, 'last_name');
        $first_guardian_name  = gv($params, 'first_guardian_name');
        $second_guardian_name = gv($params, 'second_guardian_name');
        $subject_id           = gv($params, 'subject_id');
        $attendance_method    = gv($params, 'attendance_method');
        $session              = gv($params, 'session');

        $student_attendances = $this->student_attendance->filterByDateOfAttendance($date)
                ->when($subject_id, function ($query, $subject_id) {
                    return $query->where('subject_id', $subject_id);
                })->when($session, function ($query, $session) {
                    return $query->where('session', $session);
                })->when($batch_id, function ($query, $batch_id) {
                    return $query->where('batch_id', $batch_id);
                })->get();

        $student_record_ids = array();

        foreach ($student_attendances as $student_attendance) {
            $data = $student_attendance->attendance ? $student_attendance->attendance['data'] : [];
            foreach ($data as $attendances) {
                foreach ($attendances as $attendance) {
                    $student_record_ids[] = $attendance;
                }
            }
        }

        return $this->student_record->with('student','student.parent','batch','batch.course','admission')->whereIn('id', $student_record_ids)->whereHas('student', function ($q) use ($first_name, $last_name, $first_guardian_name, $second_guardian_name) {
            $q->filterByFirstName($first_name)->filterByLastName($last_name);

            if ($first_guardian_name || $second_guardian_name) {
                $q->whereHas('parent', function ($q1) use ($first_guardian_name, $second_guardian_name) {
                    $q1->filterByFirstGuardianName($first_guardian_name)->filterBySecondGuardianName($second_guardian_name);
                });
            }

            if (\Auth::user()->hasRole(config('system.default_role.student'))) {
                $q->whereId(\Auth::user()->Student->id);
            }

            if (\Auth::user()->hasRole(config('system.default_role.parent'))) {
                $q->whereIn('id', \Auth::user()->Parent->Students->pluck('id')->all());
            }
        })->orderBy('roll_number','asc');
    }

    /**
     * Paginate all absentee students using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginateAbsentee($params)
    {
        $page_length = gv($params, 'page_length', config('config.page_length'));

        return $this->getAbsenteeData($params)->paginate($page_length);
    }

    /**
     * Get all filtered data for printing
     *
     * @param array $params
     * @return StudentRecord
     */
    public function printAbsentee($params)
    {
        return $this->getAbsenteeData($params)->get();
    }

    /**
     * Send absentee SMS
     *
     * @param array $params
     * @return null
     */
    public function sendSMSToAbsentee($params)
    {
        $filter          = gv($params, 'filter', []);
        $filter['ids']   = gv($params, 'ids', []);
        $sms             = gv($params, 'sms');
        $student_records = $this->getAbsenteeData($filter)->get();

        $data = array();
        foreach ($student_records as $student_record) {
            $new_sms = $sms;
            $new_sms = str_replace('#NAME#', $student_record->student->name, $new_sms);
            $new_sms = str_replace('#BATCH#', $student_record->batch->course->name.' '.$student_record->batch->name, $new_sms);
            $new_sms = str_replace('#FATHER_NAME#', $student_record->student->parent->first_guardian_name, $new_sms);
            $new_sms = str_replace('#DATE#', toDate($filter['date']), $new_sms);

            if (in_array($student_record->id, gv($params, 'ids', [])))
                $data[] = array('to' => $student_record->student->contact_number, 'sms' => $new_sms);
        }

        $collection = collect($data);

        foreach ($collection->chunk(config('config.max_sms_per_chunk')) as $chunk) {
            SendCustomizedSMS::dispatch($chunk);
        }
    }

    /**
     * Delete attendance
     *
     * @param array $params
     * @return null
     */
    public function delete($params = array())
    {
        $batch_id = gv($params, 'batch_id');
        $date_of_attendance = toDate(gv($params, 'date_of_attendance'));

        $class_teachers = $this->getClassTeachers($batch_id);

        $auth_user = \Auth::user();

        if (! $auth_user->can('mark-student-attendance') && $auth_user->can('mark-class-teacher-wise-student-attendance') && ! amIClassTeacherOnDate($class_teachers, $date_of_attendance)) {
            throw ValidationException::withMessages(['message' => trans('general.permission_denied')]);
        }

        if (! dateBetweenSession($date_of_attendance)) {
            throw ValidationException::withMessages(['message' => trans('academic.invalid_session_date_range')]);
        }

        $subject_id = gv($params, 'subject_id');
        $session = gv($params, 'session');

        if ($subject_id) {
            $subject = $this->subject->findOrFail($subject_id);
        }

        if (! config('config.allow_to_modify_student_attendance') && $date_of_attendance < date('Y-m-d')) {
            throw ValidationException::withMessages(['message' => trans('student.cannot_modify_attendance_of_previous_dates')]);
        }

        if (config('config.allow_to_modify_student_attendance') && $date_of_attendance < date('Y-m-d') && dateDiff(date('Y-m-d'), $date_of_attendance) > config('config.days_allowed_to_modify_student_attendance')) {
            throw ValidationException::withMessages(['message' => trans('student.can_mark_attendance_of_days', ['day' => config('config.days_allowed_to_modify_student_attendance')] )]);
        }

        if (! config('config.allow_to_mark_student_advance_attendance') && $date_of_attendance > date('Y-m-d')) {
            throw ValidationException::withMessages(['message' => trans('student.cannot_mark_attendance_of_advance_dates')]);
        }

        if (config('config.allow_to_mark_student_advance_attendance') && $date_of_attendance > date('Y-m-d') && dateDiff(date('Y-m-d'), $date_of_attendance) > config('config.days_allowed_to_mark_student_advance_attendance')) {
            throw ValidationException::withMessages(['message' => trans('student.can_mark_advance_attendance_of_days', ['day' => config('config.days_allowed_to_mark_student_advance_attendance')] )]);
        }

        $student_attendance = $this->student_attendance->whereBatchId($batch_id)->whereSubjectId($subject_id)->whereDateOfAttendance($date_of_attendance)->whereSession($session)->first();

        if (! $student_attendance) {
            throw ValidationException::withMessages(['message' => trans('student.could_not_find_attendance')]);
        }

        $student_attendance->delete();
    }

    /**
     * Update API Attendance
     * @param  array  $params
     * @return void
     */
    public function updateApiAttendance($params = array())
    {
        $admission_id        = gv($params, 'admission_id');
        $academic_session_id = gv($params, 'academic_session_id');
        $date_time           = gv($params, 'date_time');

        $student_records = $this->student_record->with('batch')->filterBySession($academic_session_id)->whereAdmissionId($admission_id)->where(function($q) {
            $q->whereNull('date_of_exit')->orWhere(function($q1) {
                $q1->whereNotNull('date_of_exit')->where('date_of_exit', '>=', today());
            });
        })->get();

        $statuses = '';
        foreach ($student_records as $student_record) {
            $batch = $student_record->batch;

            $attendance_method = $batch->getOption('default_attendance_method') ? : 'once';

            $student_attendance = $this->student_attendance->whereDateOfAttendance(today())->whereBatchId($batch->id)->first();

            if (! $student_attendance) {
                $all_student_records = $this->student_record->filterBySession($academic_session_id)->whereBatchId($batch->id)->where('date_of_entry', '<=', today())->where(function($q) {
                        $q->whereNull('date_of_exit')->orWhere(function($q1) {
                            $q1->whereNotNull('date_of_exit')->where('date_of_exit', '>=', today());
                        });
                    })->get();

                $all_student_record_ids = array();
                foreach ($all_student_records as $all_student_record) {
                    $all_student_record_ids[] = array('id' => $all_student_record->id);
                }

                $student_attendance = $this->student_attendance->forceCreate([
                    'date_of_attendance' => today(),
                    'batch_id' => $batch->id,
                    'attendance' => array(
                        'data' => $all_student_record_ids,
                        'late' => [],
                        'logs' => []
                    )
                ]);
            }

            $absentees = $student_attendance->getAttendance('data') ? : [];
            $late = $student_attendance->getAttendance('late') ? : [];
            $logs = $student_attendance->getAttendance('logs') ? : [];

            $attendance_marked = searchByKey($logs, 'id', $student_record->id);

            if ($attendance_marked) {
                $statuses .= ' Error : Duplicate Attendance';
                continue;
            }

            $class_time = $this->getClassTiming($batch);

            if ($class_time) {
                $class_date_time = Carbon::parse(date('Y-m-d').' '.$class_time);

                if ($class_date_time->diffInMinutes(Carbon::parse($date_time)) > config('config.student_late_attendance_time')) {
                    $late[] = array('id' => $student_record->id);
                }
            }

            $absentee_data = array();
            foreach ($absentees as $absentee) {
                if (gv($absentee, 'id') != $student_record->id) {
                    $absentee_data[] = $absentee;
                }
            }

            array_push($logs, array(
                'id' => $student_record->id,
                'date_time' => $date_time
            ));
            $attendance['data'] = $absentee_data;
            $attendance['logs'] = $logs;
            $attendance['late'] = $late;
            $student_attendance->attendance = $attendance;
            $student_attendance->save();
        }

        return $statuses;
    }

    /**
     * Get class timing
     * @param  Batch $batch
     * @return mixed
     */
    private function getClassTiming($batch)
    {
        $timetable = $this->timetable->with([
            'timetableAllocations',
            'timetableAllocations.classTiming',
            'timetableAllocations.classTiming.classTimingSessions',
        ])->whereBatchId($batch->id)->where('date_effective','<=',today())->orderBy('date_effective','desc')->first();

        if (! $timetable) {
            return;
        }

        $timetable_allocation = $timetable->timetableAllocations->where('day', strtolower(date('l')))->first();

        if (! $timetable_allocation) {
            return;
        }

        $class_timing = $timetable_allocation->classTiming;

        if (! $class_timing) {
            return;
        }

        $class_timing_session = $class_timing->classTimingSessions()->first();

        return optional($class_timing_session)->start;
    }
}
