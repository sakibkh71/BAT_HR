<?php

namespace App\Models\Kpi;

use Illuminate\Database\Eloquent\Model;

class KpiConfig extends Model {
    protected $table = 'bat_kpi_configs';
    protected $primaryKey = 'bat_kpi_configs_id';

    public function configRanges(){
    	return $this->hasMany('App\Models\Kpi\KpiConfigRange', 'bat_kpi_configs_id');
    }

    public function configDetails(){
    	return $this->hasMany('App\Models\Kpi\KpiConfigDetails', 'bat_kpi_configs_id');
    }
}