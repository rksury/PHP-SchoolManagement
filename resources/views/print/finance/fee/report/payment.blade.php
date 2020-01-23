@include('print.print-layout.header')
    <h2 style="text-align: center;">{{config('config.default_academic_session.name')}}</h2>
    <h2>{{trans('finance.fee_payment_report').' '.trans('general.total_result_count',['count' => count($list)])}}</h2>
    <table class="fancy-detail">
        <thead>
            <tr>
                <th>{{trans('finance.receipt_no')}}</th>
                <th>{{trans('student.name')}}</th>
                <th>{{trans('academic.batch')}}</th>
                <th>{{trans('student.first_guardian_name')}}</th>
                <th>{{trans('student.contact_number')}}</th>
                <th>{{trans('finance.amount')}}</th>
                <th>{{trans('finance.date')}}</th>
                <th>{{trans('finance.account')}}</th>
                <th>{{trans('finance.payment_method')}}</th>
            </tr>
        </thead>
        <tbody>
        	@foreach($list as $item)
        		<tr>
                    <td>{{ $item['receipt_no'] }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['batch'] }}</td>
                    <td>{{ $item['first_guardian_name'] }}</td>
                    <td>{{ $item['contact_number'] }}</td>
                    <td>{{ currency($item['amount']) }}</td>
                    <td>{{ $item['date'] }}</td>
                    <td>{{ $item['account'] }}</td>
                    <td>{{ $item['payment_method'] }}</td>
        		</tr>
        	@endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5"></th>
                <th>{{ $footer['grand_total'] }}</th>
                <th colspan="3"></th>
            </tr>
        </tfoot>
    </table>
@include('print.print-layout.footer')