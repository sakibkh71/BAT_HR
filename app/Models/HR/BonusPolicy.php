<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class BonusPolicy extends Model {
    protected $table = 'hr_emp_bonus_policys';
    protected $primaryKey = 'hr_emp_bonus_policys_id';


    public function company(){
        return $this->belongsTo('App\Models\HR\Company','bat_company_id','bat_company_id');
    }
}
