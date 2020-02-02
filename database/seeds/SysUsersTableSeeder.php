<?php

use Illuminate\Database\Seeder;

class SysUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('items')->insert([
            'username' => 'admin@apsis.com',
            'name' => 'Apsis',
            'email' => 'admin@apsis.com',
            'password' => bcrypt('123456'),
            'password_key' => '',
            'password_expire_days' => '',
            'mobile' => '',
            'date_of_birth' => '',
            'gender' => 'Male',
            'religion' => 'Muslim',
            'last_login' => '',
            'status' => 'Active',
            'user_image' => '',
            'address' => 'Apsis',
            'default_url' => '',
            'default_module_id' => 1,
            'remember_token' => '',
            'created_by' => '',
            'created_at' => '',
            'updated_by' => '',
            'updated_at' => '',
        ]);
    }
}
