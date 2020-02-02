<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class Bonus extends Model {
    protected $table = 'hr_emp_bonus';
    protected $primaryKey = 'hr_emp_bonus_id';

    public function bonusConfig(){
        return $this->belongsTo('App\Models\HR\BonusConfig','hr_emp_bonus_sheet_id','hr_emp_bonus_sheet_id');
    }
    public function employee(){
        return $this->belongsTo('App\Models\HR\Employee','sys_users_id','id');
    }
    public function department(){
        return $this->belongsTo('App\Models\HR\Department','departments_id','departments_id');
    }
    public function designation(){
        return $this->belongsTo('App\Models\HR\Designation','designations_id','designations_id');
    }
    public function grade(){
        return $this->belongsTo('App\Models\HR\Grade','hr_emp_grades_id','hr_emp_grades_id');
    }
    public function category(){
        return $this->belongsTo('App\Models\HR\Category','hr_emp_categorys_id','hr_emp_categorys_id');
    }
    public function company(){
        return $this->belongsTo('App\Models\HR\Company','bat_company_id','bat_company_id');
    }
}
