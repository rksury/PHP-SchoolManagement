@include('print.print-layout.header')
    <h2 style="text-align: center;">{{config('config.default_academic_session.name')}}</h2>
    <h2>{{trans('finance.fee_due_report').' '.trans('general.total_result_count',['count' => count($list)])}}</h2>
    <table class="fancy-detail">
        <thead>
            <tr>
                <th>{{trans('student.admission_number_short')}}</th>
                <th>{{trans('student.name')}}</th>
                <th>{{trans('academic.batch')}}</th>
                <th>{{trans('student.first_guardian_name')}}</th>
                <th>{{trans('student.contact_number')}}</th>
                <th>{{trans('finance.total')}}</th>
                <th>{{trans('finance.fee_installment_due_date')}}</th>
                <th>{{trans('finance.fee_overdue')}}</th>
                <th>{{trans('finance.late_fee')}}</th>
            </tr>
        </thead>
        <tbody>
        	@foreach($list as $item)
        		<tr>
                    <td>{{ $item['admission_number'] }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['batch'] }}</td>
                    <td>{{ $item['first_guardian_name'] }}</td>
                    <td>{{ $item['contact_number'] }}</td>
                    <td>{{ $item['total'] }}</td>
                    <td>{{ $item['due_date'] }}</td>
                    <td>{{ trans('finance.fee_overdue_day', ['day' => $item['overdue']]) }}</td>
                    <td>{{ $item['late_fee'] }}</td>
        		</tr>
        	@endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5"></th>
                <th>{{ $footer['grand_total'] }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
@include('print.print-layout.footer')