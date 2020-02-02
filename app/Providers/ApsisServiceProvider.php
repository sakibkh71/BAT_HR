<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Schema;


class ApsisServiceProvider extends ServiceProvider {
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(){
        Schema::defaultStringLength(191);
        Validator::extend('password_easy', function ($attribute, $value) {
			if( !preg_match( '/[^A-Za-z0-9]+/', $value))
			{
				return true;
			}
        });
		
		Validator::extend('password_normal', function ($attribute, $value) {
			if( !preg_match( '/[^A-Za-z0-9]+/', $value))
			{
				return true;
			}
        });
		
		Validator::extend('password_complex', function ($attribute, $value) {
			if( !preg_match( '/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/', $value))
			{
				return true;
			}
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(){
        //
    }
}
