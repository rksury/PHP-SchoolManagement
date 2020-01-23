@include('print.print-layout.header')
<table class="heading" style="width: 100%; margin-bottom: 20px;"><tr><td>{{trans('finance.receipt').' #'.$transaction->voucher_number}}</td><td style="text-align: right;">{{trans('general.date').': '.showDate($transaction->date)}}</td></tr></table>
<table class="fancy-detail">
    <tr>
        <th style="text-align: center;">Fee Receipt</th>
    </tr>
</table>
<table class="fancy-detail">
    <tbody>
        <tr>
            <td><strong>{{trans('student.name')}}</strong></td>
            <td>{{$student_record->student->name}}</td>
            <td><strong>{{trans('student.first_guardian_name')}}</strong></td>
            <td>{{optional($student_record->student->parent)->first_guardian_name}}</td>
        </tr>
        <tr>
            <td><strong>{{trans('student.admission_number')}}</strong></td>
            <td>{{$student_record->admission->number}}</td>
            <td><strong>{{trans('academic.course')}}</strong></td>
            <td>{{$student_record->batch->course->name.' '.$student_record->batch->name}}</td>
        </tr>
        <tr>
            <td><strong>{{trans('student.contact_number')}}</strong></td>
            <td>{{$student_record->student->contact_number}}</td>
            <td><strong>{{trans('student.date_of_birth')}}</strong></td>
            <td>{{showDate($student_record->student->date_of_birth)}}</td>
        </tr>
    </tbody>
</table>
<h2 style="margin-top: 20px;">{{$transaction->studentFeeRecord->feeInstallment->title}} ({{trans('finance.fee_installment_due_date').': '.showDate($transaction->studentFeeRecord->feeInstallment->due_date)}})</h2>
<table class="fancy-detail" style="margin-top: 20px;">
    <thead>
        <tr>
            <th>SNo</th>
            <th>Description</th>
            <th>Due</th>
            <th>Concession</th>
            <th style="text-align: right;">Paid</th>
        </tr>
    </thead>
    <tbody>
        @php
            $i = 1;
        @endphp
        @foreach($transaction->studentFeeRecordDetails as $student_fee_record_detail)
            @php
                $transaction_additional_fee_charge = $transaction->getOption('additional_fee_charge');
                $transaction_additional_fee_charge_amount = gv($transaction_additional_fee_charge, 'amount', 0);
                $transaction_additional_fee_charge_label = gv($transaction_additional_fee_charge, 'label');
                $transaction_additional_fee_discount = $transaction->getOption('additional_fee_discount');
                $transaction_additional_fee_discount_amount = gv($transaction_additional_fee_discount, 'amount', 0);
                $transaction_additional_fee_discount_label = gv($transaction_additional_fee_discount, 'label');

                $fee_installment_detail = $transaction->studentFeeRecord->feeInstallment->feeInstallmentDetails->firstWhere('fee_head_id', $student_fee_record_detail->fee_head_id);
                $amount = $fee_installment_detail ? $fee_installment_detail->amount : 0;
                $fee_concession = $transaction->studentFeeRecord->feeConcession;

                $optional_fee_records = $transaction->studentFeeRecord->studentOptionalFeeRecords;

                $concession_amount = 0;
                if ($fee_concession) {
                    $fee_concession_detail = $fee_concession->feeConcessionDetails->firstWhere('fee_head_id', $student_fee_record_detail->fee_head_id);
                    if ($fee_concession_detail) {
                        if ($fee_concession_detail->type == 'percent') {
                            $concession_amount = ($amount * $fee_concession_detail->amount/100);
                        } else {
                            $concession_amount = $fee_concession_detail->amount;
                        }
                    }
                }
            @endphp
            @if (! in_array($student_fee_record_detail->fee_head_id, $optional_fee_records->pluck('fee_head_id')->all()))
                <tr>
                    <td>{{$i}}</td>
                    <td style="width: 60%;" class="font-weight-bold">{{$student_fee_record_detail->FeeHead->name}}</td>
                    <td>
                        {{currency($amount,1)}}
                    </td>
                    <td>
                        {{currency($concession_amount,1)}}
                    </td>
                    <td style="text-align: right;">{{currency($student_fee_record_detail->amount,1)}}</td>
                </tr>
                @php
                    $i++;
                @endphp
            @endif
        @endforeach
        @if($transaction_additional_fee_charge && $transaction_additional_fee_charge_amount > 0)
            <tr>
                <td>{{$i}}</td>
                <td style="width: 60%;" class="font-weight-bold">{{ trans('student.additional_fee_charge') }} <small>({{$transaction_additional_fee_charge_label}})</small></td>
                <td>
                </td>
                <td>
                </td>
                <td style="text-align: right;">{{currency($transaction_additional_fee_charge_amount,1)}}</td>
            </tr>
            @php
                $i++;
            @endphp
        @endif
        @if($transaction_additional_fee_discount && $transaction_additional_fee_discount_amount > 0)
            <tr>
                <td>{{$i}}</td>
                <td style="width: 60%;" class="font-weight-bold">{{ trans('student.additional_fee_discount') }} <small>({{$transaction_additional_fee_discount_label}})</td>
                <td>
                </td>
                <td>
                </td>
                <td style="text-align: right;">(-) {{currency($transaction_additional_fee_discount_amount,1)}}</td>
            </tr>
            @php
                $i++;
            @endphp
        @endif
        @if($transaction->getOption('transport_fee'))
            <tr>
                <td>{{$i}}</td>
                <td style="width: 60%;" class="font-weight-bold">{{trans('transport.fee')}}</td>
                <td></td>
                <td></td>
                <td style="text-align: right;">{{currency($transaction->getOption('transport_fee'),1)}}</td>
            </tr>
            @php
                $i++;
            @endphp
        @endif
        @if($transaction->getOption('late_fee'))
            <tr>
                <td>{{$i}}</td>
                <td style="width: 60%;" class="font-weight-bold">{{trans('finance.late_fee')}}</td>
                <td></td>
                <td></td>
                <td style="text-align: right;">{{currency($transaction->getOption('late_fee'),1)}}</td>
            </tr>
        @endif
        <tr style="font-size: 14px;">
            <td></td>
            <td class="font-weight-bold">{{trans('finance.total_amount_received')}}</td>
            <td></td>
            <td></td>
            <td style="text-align: right;">{{currency($transaction->amount,1)}}</td>
        </tr>
    </tbody>
</table>
@if($transaction->payment_method_id)
    <table class="fancy-detail">
        <tr>
            <th style="text-align: center;">Pay Mode Information</th>
        </tr>
    </table>
    <p style="font-size: 12px; margin-left: 10px;">
        {{trans('finance.payment_method').': '.$transaction->paymentMethod->name}}
        @if($transaction->instrument_number){{trans('finance.instrument_number')}} <u>{{$transaction->instrument_number}} </u> @endif
        @if($transaction->instrument_date){{trans('finance.instrument_date')}} <u>{{showDate($transaction->instrument_date)}} </u> @endif
        @if($transaction->instrument_bank_detail){{trans('finance.instrument_bank_detail')}} <u>{{$transaction->instrument_bank_detail}} </u> @endif
        @if($transaction->instrument_clearing_date){{trans('finance.instrument_clearing_date')}} <u>{{showDate($transaction->instrument_clearing_date)}} </u> @endif
        @if($transaction->reference_number){{trans('finance.reference_number')}} <u>{{$transaction->reference_number}}</u> @endif
    </p>
@else
    <p style="font-size: 12px; margin-left: 10px;">
        {{trans('finance.payment_method').': '.trans('finance.online_payment')}} ({{$transaction->reference_number}})
    </p>
@endif
<table class="fancy-detail" style="margin-top: 20px;">
    <tbody>
        <tr>
            <td>Total</td>
            <td style="text-align: right; font-weight: bold;">{{currency($transaction->amount,1)}}</td>
        </tr>
        <tr>
            <td  style="text-align: right; font-weight: bold;" colspan="2">{{currencyInWord($transaction->amount)}}</td>
        </tr>
    </tbody>
</table>
@include('print.print-layout.signatory')