<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model {
   protected $table = 'sys_users';
   protected $primaryKey = 'id';

   public function scopeEmp($query){
       return $query->where('is_employee',1)->get();
   }
   public function employeeDepartment(){
       return $this->belongsTo('App\Models\HR\Department','departments_id','departments_id');
   }
    public function employeeDesignation(){
        return $this->belongsTo('App\Models\HR\Designation','designations_id','designations_id');
    }
    public function employeeGrade(){
        return $this->belongsTo('App\Models\HR\Grade','hr_emp_grades_id','hr_emp_grades_id');
    }
    public function company(){
        return $this->belongsTo('App\Models\HR\Company','bat_company_id','bat_company_id');
    }

}
