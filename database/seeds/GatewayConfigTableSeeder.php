<?php

use Illuminate\Database\Seeder;

class GatewayConfigTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\GatewayConfig::create([
            'merchantId' => 'XXXXXX',
            'gateway_key' => 'zarinpal',
            'gateway_name' => 'زرین پال',
            'gateway_base_url' => 'https://www.zarinpal.com'
        ]);
    }
}
