@include('print.print-layout.header')
    <h2 style="text-align: center;">{{config('config.default_academic_session.name')}}</h2>
    <table class="heading" style="width: 100%; margin-bottom: 20px;"><tr><td>{{trans('finance.receipt').' #'.$transaction->voucher_number}}</td><td style="text-align: right;">{{trans('general.date').': '.showDate($transaction->date)}}</td></tr></table>
    <table class="fancy-detail">
        <thead>
            <tr>
            	<td class="font-weight-bold">{{trans('student.name')}}</td>
            	<td>{{$registration->Student->name}}</td>
            	<td class="font-weight-bold">{{trans('student.first_guardian_name')}}</td>
            	<td>{{$registration->Student->Parent->first_guardian_name}}</td>
            </tr>
            <tr>
            	<td class="font-weight-bold">{{trans('student.contact_number')}}</td>
            	<td>{{$registration->Student->contact_number}}</td>
            	<td class="font-weight-bold">{{trans('student.course_applied')}}</td>
            	<td>{{$registration->Course->course_with_group}}</td>
            </tr
            <tr>
            	<td class="font-weight-bold">{{trans('student.date_of_birth')}}</td>
            	<td>{{showDate($registration->Student->date_of_birth)}}</td>
            	<td class="font-weight-bold">{{trans('student.date_of_registration')}}</td>
            	<td>{{showDate($registration->date_of_registration)}}</td>
            </tr>
        </thead>
    </table>
    <table class="fancy-detail" style="margin-top: 30px;">
        <thead>
            <tr>
            	<td style="width: 60%;" class="font-weight-bold">{{trans('student.registration_fee')}}</td>
            	<td style="text-align: right;">{{currency($registration->registration_fee,1)}}</td>
            </tr>
            <tr style="font-size: 14px;">
            	<td class="font-weight-bold">{{trans('finance.total_amount_received')}}</td>
            	<td style="text-align: right;">{{currency($registration->registration_fee,1)}}</td>
            </tr>
        </thead>
    </table>
    <p style="font-size: 12px; margin-left: 10px;">
        {{trans('finance.payment_method').': '.$transaction->PaymentMethod->name}}
        @if($transaction->instrument_number){{trans('finance.instrument_number')}} <u>{{$transaction->instrument_number}} </u> @endif
        @if($transaction->instrument_date){{trans('finance.instrument_date')}} <u>{{showDate($transaction->instrument_date)}} </u> @endif
        @if($transaction->instrument_bank_detail){{trans('finance.instrument_bank_detail')}} <u>{{$transaction->instrument_bank_detail}} </u> @endif
        @if($transaction->instrument_clearing_date){{trans('finance.instrument_clearing_date')}} <u>{{showDate($transaction->instrument_clearing_date)}} </u> @endif
        @if($transaction->reference_number){{trans('finance.reference_number')}} <u>{{$transaction->reference_number}}</u> @endif
    </p>
@include('print.print-layout.signatory')

