<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Insert menu categories for each restaurant
     *
     * @return void
     */
    public function run()
    {
        $restaurants = User::whereRaw("userType = 2")->get();
        $categories = ['Main Dish', 'Family Set', 'Side Dish', 'Dessert', 'Drink'];

        foreach ($restaurants as $restaurant) {
            for ($i = 0; $i < count($categories); $i++) {
                Category::create([
                    'restaurant_id' => $restaurant->id,
                    'name' => $categories[$i],
                    'order' => $i + 1
                ]);     
            }   
        }
    }
}
