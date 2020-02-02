<?php

/*
 * @pram1 will gross salary of a employee
 * @pram2 will convince bill = medical+food+tada
 *
 * */


function salary_calculation_arr($user_id, $hr_emp_grades_id, $record_log_last_id='', $salary_cal) {
    $salary_component_info = DB::table('hr_emp_salary_components')
            ->select('*')
            ->where('sys_users_id', $user_id)
            ->where('record_type', 'default')
            ->where('component_type', '<>', 'Variable')
            ->get();
    $data = array();
    foreach ($salary_component_info as $k => $val) {
        $data[$k]['sys_users_id'] = $user_id;
        $data[$k]['hr_emp_grades_id'] = $hr_emp_grades_id;
        $data[$k]['record_type'] = "employee_record";
        $data[$k]['record_ref'] = $record_log_last_id;
        $data[$k]['component_type'] = $val->component_type;
        $data[$k]['component_name'] = $val->component_name;
        $data[$k]['component_slug'] = $val->component_slug;
        $data[$k]['addition_amount'] = $val->addition_amount + ($val->addition_amount * $salary_cal);
        $data[$k]['deduction_amount'] = $val->deduction_amount + ($val->deduction_amount * $salary_cal);
        $data[$k]['auto_applicable'] = $val->auto_applicable;
    }
    //dd($data);
    return $data;
}

function salary_calculation($gross_salary) {
    $salary = [];
    $salary['basic_salary'] = ($gross_salary * 70) / 100;
    $salary['house_rent'] = ($gross_salary * 18) / 100;
    $salary['allowance'] = ($gross_salary * 12) / 100;
    return $salary;
}

function yearEarnLeaveEnjoy($emp_id) {
    $leave_days = DB::table('hr_leave_records')
            ->selectRaw('sum(leave_days) as leave_days')
            ->selectRaw('YEAR(start_date) as year')
            ->join('hr_yearly_leave_policys', 'hr_yearly_leave_policys.hr_yearly_leave_policys_name', '=', 'hr_leave_records.leave_types')
            ->where('sys_users_id', $emp_id)
            ->where('is_earn_leave', '=', '1')
            ->where('hr_leave_records.status', '=', 'Active')
            ->groupBy(DB::raw('YEAR(start_date)'))
            ->get();
    if (!empty($leave_days)) {
        $yearly_leave = [];
        foreach ($leave_days as $year) {
            $yearly_leave[$year->year] = $year->leave_days;
        }
    }
    return $yearly_leave;
}

function year_earn_leave_encash($emp_id) {
    $leave_encashments = DB::table('hr_leave_encashments')
            ->selectRaw('sum(encashment_days) as encashment_days')
            ->selectRaw('YEAR(encashment_date) as year')
            ->where('sys_users_id', $emp_id)
            ->where('status', '=', 'Active')
            ->groupBy(DB::raw('YEAR(encashment_date)'))
            ->get();

    if (!empty($leave_encashments)) {
        $yearly_leave_encash = [];
        foreach ($leave_encashments as $year) {
            $yearly_leave_encash[$year->year] = $year->encashment_days;
        }
    }
    return $yearly_leave_encash;
}

function employeeInfo($user_id, $pdf = false) {
    $data['emp_log'] = DB::table('sys_users')
            ->select(
                    'sys_users.*', 'designations.designations_name', 'bat_company.company_name as distributor_house', 'bat_distributorspoint.name as distributor_point')
            ->leftJoin('designations', 'designations.designations_id', '=', 'sys_users.designations_id')
            ->leftJoin('bat_company', 'bat_company.bat_company_id', '=', 'sys_users.bat_company_id')
            ->leftJoin('bat_distributorspoint', 'bat_distributorspoint.id', '=', 'sys_users.bat_dpid')
            ->where('sys_users.id', $user_id)
            ->first();
    if ($pdf) {
        return view('HR.emp_info_pdf', $data);
    } else {
        return view('HR.emp_info', $data);
    }
}

function tdFormatter($key) {
    if (strpos($key, 'right_') !== false) {
        return trim($key, 'right_');
    } else if (strpos($key, 'right_') !== false) {
        return trim($key, 'center_');
    } else {
        return $key;
    }
}

function tdDataFormatter($key, $val) {
    if (strpos($key, 'right_') !== false) {
        return "<td align='right'>$val</td>";
    } else if (strpos($key, 'center_') !== false) {
        return "<td align='center'>$val</td>";
    } else {
        return "<td>$val</td>";
    }
}

function bonus_policy($emp_info, $eligible_date) {
    $policy = DB::table('hr_emp_bonus_policys')
            ->where('bat_company_id', $emp_info->bat_company_id ? $emp_info->bat_company_id : 1)
            ->orderBy('bonus_eligible_based_on', 'ASC')
            ->get();
    if($emp_info->date_of_join!=null || $emp_info->date_of_confirmation!=null) {
        $date_of_join = new DateTime($emp_info->date_of_join);
        $date_of_confirmation = new DateTime($emp_info->date_of_confirmation);
        $eligible_date = new DateTime($eligible_date);

        $joining_diff = $eligible_date->diff($date_of_join);
        $confirm_diff = $eligible_date->diff($date_of_confirmation);
        $joining_diff_m = ($joining_diff->y * 12) + $joining_diff->m;
        $confirm_diff_m = ($confirm_diff->y * 12) + $confirm_diff->m;
        $bonus_base_on = 0;
        $bonus_amount = 0;
        foreach ($policy as $p) {
            if ($p->bonus_based_on == 'basic') {
                $bonus_base_on = $emp_info->basic_salary;
            } else {
                $bonus_base_on = $emp_info->min_gross;
            }
            if ($p->bonus_eligible_based_on == 'date_of_join') {

                if ($joining_diff_m >= $p->number_of_month) {

                    return array(
                        'bonus_policys_id' => $p->hr_emp_bonus_policys_id,
                        'bonus_policy' => ucwords($p->bonus_based_on) . '*' . $p->bonus_ratio . '%',
                        'bonus_amount' => ($p->bonus_ratio * $bonus_base_on) / 100,
                        'bonus_eligible_based_on' => $p->bonus_eligible_based_on,
                        'bonus_based_on' => $p->bonus_based_on,
                        'total_month' => $joining_diff_m,
                    );
                }
            } else {
                if ($confirm_diff_m >= $p->number_of_month) {
                    return array(
                        'bonus_policys_id' => $p->hr_emp_bonus_policys_id,
                        'bonus_policy' => ucwords($p->bonus_based_on) . '*' . $p->bonus_ratio . '%',
                        'bonus_amount' => ($p->bonus_ratio * $bonus_base_on) / 100,
                        'bonus_eligible_based_on' => $p->bonus_eligible_based_on,
                        'bonus_based_on' => $p->bonus_based_on,
                        'total_month' => $joining_diff_m,
                    );
                }
            }
        }
    }else{
        return false;
    }
    return $bonus_amount;
}

function bonus_policy_manual($emp_info, $eligible_date, $manual_data = []) {
    $date_of_join = new DateTime($emp_info->date_of_join);
    $date_of_confirmation = new DateTime($emp_info->date_of_confirmation);
    $eligible_date = new DateTime($eligible_date);

    $joining_diff = $eligible_date->diff($date_of_join);
    $confirm_diff = $eligible_date->diff($date_of_confirmation);
    $joining_diff_m = ($joining_diff->y * 12) + $joining_diff->m;
    $confirm_diff_m = ($confirm_diff->y * 12) + $confirm_diff->m;

    if ($manual_data['bonus_eligible_based_on'] == 'date_of_join') {
        $number_of_month = $joining_diff_m;
    } else {
        $number_of_month = $confirm_diff_m;
    }
    $q = DB::table('hr_emp_bonus_policys')
                    ->selectRaw('ifnull(MAX(bonus_ratio),0) as bonus_ratio')
                    ->where('hr_emp_categorys_id', $emp_info->hr_emp_categorys_id)
                    ->where('number_of_month', '<=', $number_of_month)
                    ->get()->first();
    $policy_ratio = $q->bonus_ratio;
    $bonus_base_on = 0;
    $bonus_amount = 0;
    if ($manual_data['bonus_based_on'] == 'basic') {
        $bonus_base_on = $emp_info->basic_salary;
    } else {
        $bonus_base_on = $emp_info->min_gross;
    }
    return array(
        'bonus_policys_id' => null,
        'bonus_policy' => ucwords($manual_data['bonus_based_on']) . '*' . $policy_ratio . '%',
        'bonus_amount' => ($policy_ratio * $bonus_base_on) / 100,
        'bonus_eligible_based_on' => $manual_data['bonus_eligible_based_on'],
        'bonus_based_on' => $manual_data['bonus_based_on'],
        'total_month' => $number_of_month,
    );
}

function draft_limit_check($table, $status_col, $draft_id) {
    $draft_limit = getOptionValue($table);
    $draft_limit = $draft_limit ? $draft_limit : 3;
    $draft_exists = DB::table($table)
                    ->selectRaw('count(*) as total_draft')
                    ->where('created_by', Auth::id())
                    ->where($status_col, $draft_id)
                    ->where('status', 'Active')
                    ->get()
                    ->first()->total_draft;
    if ($draft_exists >= $draft_limit) {
        return false;
    }

    return true;
}

function getUoMIdBySlug($slug = '') {
    return $uom_id = DB::table('product_uoms')->where('short_name', '=', $slug)->value('product_uoms_id');
}

function getUoMShortNameFromId($ids) {
    $sql = DB::table('product_uoms')->select('short_name');
    if (is_array($ids)) {
        $sql->whereIn('product_uoms_id', $ids);
        $sql_result = $sql->get()->toArray();
        $array_result = array_column($sql_result, 'short_name');
        $result = $array_result;
    } else {
        $sql->where('product_uoms_id', $ids);
        $result = $sql->first()->short_name;
    }
    return $result;
}

function getHouseLogo($house = null) {
    if ($house == null) {
        $p_house = session()->get('PRIVILEGE_HOUSE');
        $house = is_array($p_house) ? $p_house[0] : $p_house;
    }

    $company = DB::table('bat_company')->select('logo', 'company_name')->where('bat_company_id', $house)->first();

    $result = '<a href="' . URL::to('/') . '">';
    if (!empty($company->logo)) {
        $result .= '<span class="logo"><img alt="image" class="img-responsive" style="max-width: 100%"  src="' . asset('public/img/company_logo/' . $company->logo) . '"/></span>';
        $result .= '<h3>'.$company->company_name.'</h3>';
    } elseif (!empty($company->company_name)) {
        $result .= '<span class="logo">'. $company->company_name .'</span>';
    } else {
        $result .= '<span class="logo"><img alt="image" class="img-responsive" width="80px"  src="' . asset(getOptionValue('company_logo2')) . '"/></span>';
    }
    $result .= '</a>';

    return $result;
}
