<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sys_menus';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
                  'name',
                  'menus_description',
                  'menus_type',
                  'parent_menus_id',
                  'modules_id',
                  'icon_class',
                  'menu_url',
                  'sort_number',
                  'created_by',
                  'updated_by',
                  'status'
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
     * Get the parentMenu for this model.
     */
    public function parentMenu()
    {
        return $this->belongsTo('App\Models\Menu','parent_menus_id');
    }

    /**
     * Get the module for this model.
     */
    public function module()
    {
        return $this->belongsTo('App\Models\Module','modules_id');
    }

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
