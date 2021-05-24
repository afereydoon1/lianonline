<?php

use Illuminate\Database\Seeder;

class SiteInfoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\SiteInfo::create([
            'site_name' => 'LianShop',
            'site_description' => 'LianShop Description',
            'logo_url' => '',
            'favicon_url' => '',
            'licenses' => [
                'test1' => '<h2>test1</h2>',
                'test2' => '<h2>test2</h2>',
            ],
            'gateway' => '<h3>gateway info</h3>'
        ]);
    }
}
