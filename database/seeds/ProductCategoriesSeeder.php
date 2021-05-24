<?php

use Illuminate\Database\Seeder;

class ProductCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Category::create([
            'title' => 'هنر',
            'slug' => 'هنر',
            'briefly' => 'محصولات هنری',
            'parent_id' => null,
            'order' => 1
        ]);
    }
}
