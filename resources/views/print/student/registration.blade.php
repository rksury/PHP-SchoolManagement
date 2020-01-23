@include('print.print-layout.header')
    <h2 style="text-align: center;">{{config('config.default_academic_session.name')}}</h2>
    <h2>{{trans('student.registration').' '.trans('general.total_result_count',['count' => count($registrations)])}}</h2>
    <table class="fancy-detail">
        <thead>
            <tr>
                <th>{{trans('student.name')}}</th>
                <th>{{trans('student.first_guardian_name')}}</th>
                <th>{{trans('student.date_of_birth')}}</th>
                <th>{{trans('student.contact_number')}}</th>
                <th>{{trans('academic.course')}}</th>
                <th>{{trans('student.registration_status')}}</th>
                <th>{{trans('student.date_of_registration')}}</th>
                <th>{{trans('student.registration_fee')}}</th>
            </tr>
        </thead>
        <tbody>
        	@foreach($registrations as $registration)
        		<tr>
        			<td>{{$registration->Student->name}}</td>
                    <td>{{optional($registration->Student->Parent)->first_guardian_name}}</td>
                    <td>{{showDate($registration->Student->date_of_birth)}}</td>
                    <td>{{$registration->Student->contact_number}}</td>
                    <td>{{$registration->Course->name.' ('.$registration->Course->CourseGroup->name.')'}}</td>
                    <td>{{trans('student.registration_status_'.$registration->status)}}</td>
                    <td>{{showDate($registration->date_of_registration)}}</td>
                    <td>
                        @if($registration->registration_fee)
                            {{currency($registration->registration_fee,1)}}
                            ({{$registration->registration_fee_status == 'paid' ? trans('student.registration_fee_status_paid') : trans('student.registration_fee_status_unpaid')}})
                        @endif
                    </td>
        		</tr>
        	@endforeach
        </tbody>
    </table>
@include('print.print-layout.footer')