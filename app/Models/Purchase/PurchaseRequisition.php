<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequisition extends Model
{


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'purchase_requisitions';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'purchase_requisitions_id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [

    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * Get the creator for this model.
     */
    public function creator()
    {
        return $this->belongsTo('App\User','created_by');
    }

    /**
     * Get the updater for this model.
     */
    public function updater()
    {
        return $this->belongsTo('App\User','updated_by');
    }

   /**
     * Get created_at in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getCreatedAtAttribute($value)
    {
        return date('j/n/Y g:i A', strtotime($value));
    }

    /**
     * Get updated_at in array format
     *
     * @param  string  $value
     * @return array
     */
    public function getUpdatedAtAttribute($value)
    {
        return date('j/n/Y g:i A', strtotime($value));
    }

}
