<?php

use Illuminate\Database\Seeder;

class SmsConfigTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\SmsConfig::create([
            'api_key' => 'afca3a5614b4aa7d80be45b1',
            'api_secret' => '5N2v3uhGrMLYPxF4',
            'api_number' => '50002015973333',
            'api_url' => 'https://RestfulSms.com/api/'
        ]);
    }
}
