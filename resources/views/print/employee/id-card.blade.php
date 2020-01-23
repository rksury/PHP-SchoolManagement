<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>{{config('app.name')}}</title>

	<style>
		*{font-family:'Helvetica'; font-size: 14px; color: #ffffff; }
	    body{width:auto; max-width:800px;margin:0 auto;font-size:12px; margin-top: 20px;}
	    p {line-height: 14px;}
	</style>
</head>
<body>
	@foreach($employees as $employee)
		<div style="width:{{$id_card_template->width}}mm; height:{{$id_card_template->height}}mm; padding:0px 0 0 10px; border: 0px solid black; margin-bottom: 10px; margin-right: 10px; float:left; @if($id_card_template->getOption('background_image')) background-image: url('{{url($id_card_template->getOption('background_image'))}}'); background-repeat: no-repeat; background-size: cover; @endif">
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td valign="middle">
						<p>{{trans('employee.name')}}: <br /> <strong>{{$employee->name}}</strong></p>
						<p>{{trans('employee.father_name')}}: <br /> <strong>{{$employee->father_name}}</strong></p>
						<p>{{trans('employee.mother_name')}}: <br /> <strong>{{$employee->mother_name}}</strong></p>
						<p>{{trans('employee.date_of_birth')}}: <br /> <strong>{{showDate($employee->date_of_birth)}}</strong></p>
						<p>{{trans('employee.designation')}}: <br /> <strong><span style="font-size: 90%;">{{getEmployeeDesignationName($employee)}}</span></strong></p>
						<p>{{trans('employee.code')}}: <br /> <strong>{{$employee->employee_code}}</strong></p>
					</td>
					<td valign="middle" align="center" style="max-width: 110px;">
						@if($employee->photo)
							<img src="{{url($employee->photo)}}" style="max-width: 100px;">
						@else
							<img src="{{$employee->gender == 'male' ? url('/images/male.png') : url('/images/female.png')}}" style="max-width: 100px;">
						@endif

						@if($id_card_template->getOption('signature_image'))
							<div style="font-size: 90%; margin: 10px 0;">{{trans('general.authorized_signatory')}}</div>
							<img src="{{url($id_card_template->getOption('signature_image'))}}" style="max-width: 100px; max-height: 40px;">
						@endif
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<p style="line-height: 14px;">
							<span style="font-size:110%; font-weight: bold;">{{config('config.institute_name')}}</span> {{config('config.institute_recognition_number')}} <br />
							{{config('config.address_line_1')}}
                        	@if(config('config.address_line_2')), {{config('config.address_line_2')}} @endif
                        	@if(config('config.city')), {{config('config.city')}} @endif
                        	@if(config('config.state')), {{config('config.state')}} @endif
                        	@if(config('config.zipcode')), {{config('config.zipcode')}} @endif
                        	@if(config('config.country')), {{config('config.country')}} @endif
                        	{{config('config.phone')}} {{config('config.email')}}
						</p>
					</td>
				</tr>
			</table>
		</div>

		@if($loop->iteration % $id_card_template->getOption('per_page_limit') === 0)
			<div style="page-break-after: always;"></div>
		@endif
	@endforeach
</body>
</html>