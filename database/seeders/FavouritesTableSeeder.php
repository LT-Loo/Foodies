<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Dish;
use App\Models\Favourite;

class FavouritesTableSeeder extends Seeder
{
    /**
     * Insert favourite dishes for customers
     *
     * @return void
     */
    public function run()
    {
        $customers = User::select('id')->whereRaw("userType = 1")->get();
        $dishes = Dish::all();
        
        foreach ($customers as $customer) {
            $faveNum = rand(0, 6);
            $faves = [];
            for($i = 0; $i < $faveNum; $i++) {
                
                // Check for duplicate dish
                $dish = $dishes[rand(0, count($dishes) - 1)];
                while (in_array($dish->id, $faves)) {
                    $dish = $dishes[rand(0, count($dishes) - 1)];
                }

                Favourite::create([
                    'customer_id' => $customer->id,
                    'dish_id' => $dish->id,
                    'created_at' => time()
                ]);

                $faves[] = $dish->id;
            }
        }
    }
}
