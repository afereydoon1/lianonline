<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Role::create([
            'key' => 'god',
            'title' => 'مدیر کل سایت',
            'is_admin' => true
        ]);
    }
}
