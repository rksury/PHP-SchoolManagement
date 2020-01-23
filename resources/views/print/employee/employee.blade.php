@include('print.print-layout.header')
    <h2 style="text-align: center;">{{config('config.default_academic_session.name')}}</h2>
    <h2>{{trans('employee.employee').' '.trans('general.total_result_count',['count' => count($employees)])}}</h2>
    <table class="fancy-detail">
        <thead>
            <tr>
                <th>{{trans('employee.code')}}</th>
                <th>{{trans('employee.name')}}</th>
                <th>{{trans('employee.status')}}</th>
                <th>{{trans('employee.father_name')}}</th>
                <th>{{trans('employee.date_of_birth')}}</th>
                <th>{{trans('employee.contact_number')}}</th>
                <th>{{trans('employee.department')}}</th>
                <th>{{trans('employee.designation')}}</th>
                <th>{{trans('employee.date_of_joining')}}</th>
            </tr>
        </thead>
        <tbody>
        	@foreach($employees as $employee)
        		<tr>
        			<td>{{$employee->employee_code}}</td>
                    <td>{{$employee->name}}</td>
                    <td>
                        <?php
                            $employee_term = $employee->EmployeeTerms->first();
                        ?>
                        @if($employee_term && $employee_term->date_of_joining <= date('Y-m-d') && (! $employee_term->date_of_leaving || $employee->date_of_leaving >= date('Y-m-d')))
                            {{trans('employee.status_active')}}
                        @else
                            {{trans('employee.status_inactive')}}
                        @endif
                    </td>
                    <td>{{$employee->father_name}}</td>
                    <td>{{showDate($employee->date_of_birth)}}</td>
                    <td>{{$employee->contact_number}}</td>
                    <td>
                        <?php
                            $employee_designation = $employee->EmployeeDesignations->first();
                        ?>
                        @if($employee_designation && $employee_designation->department_id)
                            {{$employee_designation->Department->name}}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <?php
                            $employee_designation = $employee->EmployeeDesignations->first();
                        ?>
                        @if($employee_designation && $employee_designation->designation_id)
                            {{$employee_designation->Designation->designation_with_category}}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($employee_term) {{showDate($employee_term->date_of_joining)}} @endif
                    </td>
        		</tr>
        	@endforeach
        </tbody>
    </table>
@include('print.print-layout.footer')