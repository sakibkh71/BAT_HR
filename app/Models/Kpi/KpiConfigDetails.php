<?php

namespace App\Models\Kpi;

use Illuminate\Database\Eloquent\Model;

class KpiConfigDetails extends Model {
    protected $table = 'bat_kpi_config_details';
    protected $primaryKey = 'bat_kpi_config_details_id';

    public function propertyName(){
    	return $this->belongsTo('App\Models\Kpi\KpiProperties','bat_kpi_properties_id','bat_kpi_properties_id');
    }
}