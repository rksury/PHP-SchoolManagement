@include('print.print-layout.header',compact('print_options'))
    <h2 style="text-align: center;">{{trans('exam.report_card')}} {{config('config.default_academic_session.name')}}</h2>

    <table class="report-card" style="margin-top: 20px;">
        <tbody>
        	<tr>
				<td style="width: 15%; font-weight: bold;">{{trans('student.name')}}</td>
				<td style="width: 35%;">{{$student_record->student->name}}</td>
				<td style="width: 10%; font-weight: bold;">{{trans('student.admission_number_short')}}</td>
				<td style="width: 15%;">{{$student_record->admission->prefix.numberPadding($student_record->admission->number,config('config.admission_number_digit'))}}</td>
				<td style="width: 10%; font-weight: bold;">{{trans('student.roll_number')}}</td>
				<td style="width: 15%;">{{getRollNumber($student_record)}}</td>
			</tr>
        	<tr>
				<td style="width: 15%; font-weight: bold;">{{trans('student.father_name')}}</td>
				<td style="width: 35%;">{{$student_record->student->parent->father_name}}</td>
				<td style="width: 10%; font-weight: bold;">{{trans('student.date_of_admission')}}</td>
				<td style="width: 15%;">{{showDate($student_record->admission->date_of_admission)}}</td>
				<td style="width: 10%; font-weight: bold;">{{trans('student.date_of_birth')}}</td>
				<td style="width: 15%;">{{showDate($student_record->student->date_of_birth)}}</td>
			</tr>
        	<tr>
				<td style="width: 15%; font-weight: bold;">{{trans('student.mother_name')}}</td>
				<td style="width: 35%;">{{$student_record->student->parent->mother_name}}</td>
				<td style="width: 10%; font-weight: bold;">{{trans('academic.batch')}}</td>
				<td style="width: 15%;">{{$student_record->batch->course->name.' '.$student_record->batch->name}}</td>
				<td style="width: 10%; font-weight: bold;"></td>
				<td style="width: 15%;"></td>
			</tr>
        </tbody>
    </table>

    <table class="report-card" style="margin-top: 20px;">
        <tbody>
        	@if (! gv($summary, 'term_header'))
		        <tr>
					<td rowspan="2">#</td>
					<td rowspan="2" style="text-align: center; font-weight: bold; font-size: 120%">{{trans('academic.subject')}}</td>
					@foreach ($summary['header'] as $header)
						<td colspan="{{count($header['assessment_details'])}}" style="text-align: center; font-weight: bold; font-size: 120%">{{$header['name']}}</td>
					@endforeach
				</tr>
			@else
		        <tr>
					<td rowspan="3">#</td>
					<td rowspan="3" style="text-align: center; font-weight: bold; font-size: 120%">{{trans('academic.subject')}}</td>
					@foreach ($summary['term_header'] as $term_header)
						<td colspan="{{gv($term_header, 'colspan')}}" style="text-align: center; font-weight: bold; font-size: 120%">{{$term_header['name']}}</td>
					@endforeach
				</tr>

				<tr>
					@foreach ($summary['header'] as $header)
						<td colspan="{{count($header['assessment_details'])}}" style="text-align: center; font-weight: bold; font-size: 120%">{{$header['name']}}</td>
					@endforeach
				</tr>
			@endif
        	<tr>
				@foreach ($summary['header'] as $header)
					@foreach ($header['assessment_details'] as $assessment)
						<td style="text-align: center; font-weight: bold;">{{gv($assessment, 'code')}} 
							@if (! gbv($assessment, 'is_grading') && gv($assessment, 'id') == 0 && gv($assessment, 'max_mark'))
								({{formatNumber($assessment['max_mark'])}})
							@endif
						</td>
					@endforeach
				@endforeach
			</tr>

			@foreach ($summary['subjects'] as $subject)
			<tr>
				<td>{{$loop->index + 1}}</td>
				<td>
					{{$subject['name']}}
					@if (gv($subject, 'shortcode'))
						({{gv($subject, 'shortcode')}})
					@endif
				</td>
				@foreach($subject['marks'] as $mark)
					<td style="text-align: center;">{{$mark}}</td>
				@endforeach
			</tr>
			@endforeach

        	<tr>
				<td rowspan="3"></td>
				<td>{{trans('exam.total')}}</td>
				@foreach ($summary['subject_assessment_total'] as $subject_assessment_total)
					@foreach ($subject_assessment_total as $value)
						<td style="text-align: center; font-weight: bold;">{{is_numeric($value) ? formatNumber($value) : $value}}</td>
					@endforeach
				@endforeach
			</tr>

        	<tr>
				<td>{{trans('exam.grand_total')}}</td>
				@foreach ($summary['header'] as $header)
					<td colspan="{{count($header['assessment_details'])}}" style="text-align: center; font-weight: bold; font-size: 120%">
						@if (is_numeric($summary['exam_total'][$header['schedule_id']]))
							{{$summary['exam_total'][$header['schedule_id']]}} / {{$summary['exam_assessment_total'][$header['schedule_id']]}}
						@endif
					</td>
				@endforeach
			</tr>

        	<tr>
				<td>{{trans('exam.percentage')}}</td>
				@foreach ($summary['header'] as $header)
					<td colspan="{{count($header['assessment_details'])}}" style="text-align: center; font-weight: bold; font-size: 120%">
						@if (is_numeric($summary['exam_total'][$header['schedule_id']]))
							@php
								$percentage = formatNumber(($summary['exam_total'][$header['schedule_id']] / $summary['exam_assessment_total'][$header['schedule_id']] ) * 100);
								$overall_pass_percentage = gv($header, 'overall_pass_percentage');
							@endphp
							{{$percentage}}% 
							@if ($overall_pass_percentage)
								<span style="text-transform: uppercase;"> {{$percentage >= $overall_pass_percentage ? trans('exam.exam_result_passed') : trans('exam.exam_result_failed') }}</span>
							@endif
						@endif
					</td>
				@endforeach
			</tr>
        </tbody>
    </table>
	
	<div class="page-break"></div>

	<table border="0" style="width: 100%;">
		<tr>
			<td style="width: 65%;" valign="top">
				@if (count($summary['observation_params']))
				    <table class="report-card" style="margin-top: 20px;">
				        <tbody>
				        	@if (! gv($summary, 'observation_term_header'))
						        <tr>
									<td>#</td>
									<td style="text-align: center; font-weight: bold; font-size: 120%">{{trans('exam.observation')}}</td>
									@foreach ($summary['observation_header'] as $header)
										<td style="text-align: center; font-weight: bold; font-size: 120%">{{$header['name']}}</td>
									@endforeach
								</tr>
							@else
						        <tr>
									<td rowspan="2">#</td>
									<td rowspan="2" style="text-align: center; font-weight: bold; font-size: 120%">{{trans('exam.observation')}}</td>
									@foreach ($summary['observation_term_header'] as $term_header)
										<td colspan="{{gv($term_header, 'colspan')}}" style="text-align: center; font-weight: bold; font-size: 120%">{{$term_header['name']}}</td>
									@endforeach
								</tr>

								<tr>
									@foreach ($summary['observation_header'] as $header)
										<td style="text-align: center; font-weight: bold; font-size: 120%">{{$header['name']}}</td>
									@endforeach
								</tr>
							@endif

							@foreach ($summary['observation_params'] as $param)
							<tr>
								<td>{{$loop->index + 1}}</td>
								<td>{{$param['name']}}</td>
								@foreach($param['marks'] as $mark)
									<td style="text-align: center;">{{$mark}}</td>
								@endforeach
							</tr>
							@endforeach
						</tbody>
					</table>
				@endif
			    <table class="report-card" style="margin-top:10px;">
			        <tbody>
						<tr>
							<td style="font-weight: bold;">{{trans('exam.total_working_days')}}</td>
							<td style="text-align: right;">{{gv($summary, 'working_days')}}</td>
						</tr>
						<tr>
							<td style="font-weight: bold;">{{trans('exam.total_attendances')}}</td>
							<td style="text-align: right;">{{gv($summary, 'attendance')}}</td>
						</tr>
						<tr>
							<td style="font-weight: bold;">{{trans('exam.attendance_percentage')}}</td>
							<td style="text-align: right;">{{gv($summary, 'attendance_percentage')}}</td>
						</tr>
			        </tbody>
			    </table>
			</td>
			<td style="width: 35%;" valign="top">
				@if ($summary['grade'])
				    <table class="report-card" style="margin-top: 20px;">
						<tr>
							<td colspan="3" style="text-align: center; font-weight: bold; font-size: 120%">{{trans('exam.grade')}}</td>
						</tr>
						@foreach ($summary['grade']->details as $detail)
							<tr>
								<td style="text-align: center;">{{$detail->name}}</td>
								<td style="text-align: center;">{{$detail->description}}</td>
								<td style="text-align: center;">{{trans('exam.grade_detail', ['min_percentage' => formatNumber($detail->min_percentage), 'max_percentage' => formatNumber($detail->max_percentage)])}}</td>
							</tr>
						@endforeach
				    </table>
				@endif
			</td>
		</tr>
	</table>

	<table border="0" style="width:100%; border:0px; margin-top: {{gv(isset($print_options) ? $print_options : [], 'margin_before_signature')}};">
		<tr style="height: 70px;" valign="bottom">
			<td width="33%">{{trans('exam.signature_class_teacher')}}</td>
			<td width="34%" style="text-align: center;">{{trans('exam.signature_exam_incharge')}}</td>
			<td width="33%" style="text-align: right;">{{trans('exam.signature_principal')}}</td>
		</tr>
	</table>
