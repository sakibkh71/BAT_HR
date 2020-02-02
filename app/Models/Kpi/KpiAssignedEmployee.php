<?php

namespace App\Models\Kpi;

use Illuminate\Database\Eloquent\Model;

class KpiAssignedEmployee extends Model {
    protected $table = 'bat_kpi_assigned_employee';
    protected $primaryKey = 'bat_kpi_assigned_employee_id';

    public function designation(){
    	return $this->belongsTo('App\Models\Kpi\Designations','designations_id','designations_id');
    }

    public function property(){
    	return $this->belongsTo('App\Models\Kpi\KpiProperties','bat_kpi_properties_id','bat_kpi_properties_id');
    }
    
}