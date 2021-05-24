<?php

use Illuminate\Database\Seeder;

class PostAssortmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Assortment::create([
            'title' => 'اطلاعیه ها',
            'slug' => 'اطلاعیه-ها',
            'briefly' => 'اطلاعیه های مهم سایت',
            'parent_id' => null,
            'order' => 1
        ]);
    }
}
