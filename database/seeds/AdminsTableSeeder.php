<?php

use Illuminate\Database\Seeder;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Admin::create([
            'name' => 'مدیر کل سایت',
            'username' => 'lianadmin',
            'email' => 'behroozahmadi1372@yahoo.com',
            'mobile' => '09370923593',
            'password' => bcrypt('123456'),
            'role_id' => 1,
            'email_verified_at' => \Carbon\Carbon::now(),
            'mobile_verified_at' => \Carbon\Carbon::now(),
        ]);
        \App\Models\Admin::create([
            'name' => 'مصطفی احمدی',
            'username' => 'MostafaAhmadi',
            'email' => 'Mot.ahmadi@yahoo.com',
            'mobile' => '09111111111',
            'password' => bcrypt('123456789'),
            'role_id' => 1,
            'email_verified_at' => \Carbon\Carbon::now(),
            'mobile_verified_at' => \Carbon\Carbon::now(),
        ]);
    }
}
