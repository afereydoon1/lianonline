<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call(RolesTableSeeder::class);
         $this->call(AdminsTableSeeder::class);
         $this->call(WalletTableSeeder::class);
         $this->call(ProductCategoriesSeeder::class);
         $this->call(ProductTagsSeeder::class);
         $this->call(PostAssortmentsSeeder::class);
         $this->call(SiteInfoTableSeeder::class);
         $this->call(SmsConfigTableSeeder::class);
         $this->call(GatewayConfigTableSeeder::class);
    }
}
