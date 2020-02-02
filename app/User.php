<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'username',
        'sys_users_name',
        'email',
        'password',
        'password_key',
        'password_expire_days',
        'mobile',
        'date_of_birth',
        'gender',
        'religion',
        'last_login',
        'status',
        'user_image',
        'address',
        'default_url',
        'default_module_id',
        'remember_token',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
	
	protected $table = 'sys_users';

    public function findForPassport($identifier) {
        return User::orWhere('email', $identifier)->where('status', 'Active')->first();
    }
}
