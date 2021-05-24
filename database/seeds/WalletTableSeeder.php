<?php

use App\Models\Admin;
use Illuminate\Database\Seeder;

class WalletTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $Admin = Admin::first();
        \App\Models\Wallet::create([
            'user_id'=> $Admin->id,
            'user_type'=> 'App\Models\Admin',
            'amount'=> 0
        ]);
        $Admin2 = Admin::where('email','Mot.ahmadi@yahoo.com')->first();
        \App\Models\Wallet::create([
            'user_id'=> $Admin2->id,
            'user_type'=> 'App\Models\Admin',
            'amount'=> 0
        ]);
    }
}
