@include('print.print-layout.header')
    <h2 style="text-align: center;">{{config('config.default_academic_session.name')}}</h2>
    <h2>{{trans('finance.transaction_day_book_report').' '.trans('general.total_result_count',['count' => count($list)])}}</h2>
    <table class="fancy-detail">
        <thead>
            <tr>
                <th>#</th>
                <th>{{trans('finance.voucher_number')}}</th>
                <th>{{trans('finance.date')}}</th>
                <th>{{trans('finance.payment')}}</th>
                <th>{{trans('finance.receipt')}}</th>
                <th>{{trans('general.description')}}</th>
                <th>{{trans('finance.account')}}</th>
                <th>{{trans('finance.payment_method')}}</th>
                <th>{{trans('general.entry_by')}}</th>
            </tr>
        </thead>
        <tbody>
        	@foreach($list as $index => $item)
        		<tr>
                    <td>{{$index+1}}</td>
                    <td>{{gv($item, 'voucher_number')}}</td>
                    <td>{{showDate(gv($item, 'date'))}}</td>
                    <td>{{gv($item, 'type') == 'payment' ? gv($item, 'amount') : '-'}}</td>
                    <td>{{gv($item, 'type') == 'receipt' ? gv($item, 'amount') : '-'}}</td>
                    <td>{{gv($item,'head')}}</td>
                    <td>{{gv($item, 'account')}}</td>
                    <td>
                        {{gv($item, 'payment_method')}}
                        @if (gv($item, 'payment_method_detail'))
                            {!! gv($item, 'payment_method_detail') !!}
                        @endif
                    </td>
                    <td>{{gv($item,'employee')}}</td>
        		</tr>
        	@endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3"></th>
                <th>{{gv($footer,'total_payments')}}</th>
                <th>{{gv($footer,'total_receipts')}}</th>
                <th colspan="4"></th>
            </tr>
        </tfoot>
    </table>
@include('print.print-layout.footer')