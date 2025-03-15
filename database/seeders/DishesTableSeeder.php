<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Dish;

class DishesTableSeeder extends Seeder
{
    /**
     * Insert dishes for each restaurant
     *
     * @return void
     */
    public function run()
    {
        $fakerDesc = \Faker\Factory::create();
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \Gbuckingham89\FakerFood\en_GB\FoodProvider($faker));

        $restaurants = User::whereRaw("userType = 2")->get();
        foreach ($restaurants as $restaurant) { // For every restaurant
            $categories = $restaurant->categories;
            $faker->unique($reset = true); // Reset unique feature

            foreach ($categories as $category) { // Generate random dishes for every category
                $num = rand(1, 10);
                $promo = [0, 0, 0, 10, 20, 30, 40];

                for ($i = 1; $i <= $num; $i++) { // Generate random dish name accordingly
                    if ($category->name == "Drink") {
                        if ($i % 2 == 0) {$dish = $faker->unique()->foodBeverageAlcoholic();}
                        else {$dish = $faker->unique()->foodBeverageNonAlcoholic();}
                    }
                    else {
                        $choice = rand(1, 6);
                        switch ($choice) {
                            case 1:
                                $dish = $faker->unique()->foodNoodle();
                                break;
                            case 2:
                                $dish = $faker->unique()->foodMeat();
                                break;
                            case 3:
                                $dish = $faker->unique()->foodPasta();
                                break;
                            case 4:
                                $dish = $faker->unique()->foodDairySolid();
                                break;
                            case 5:
                                $dish = $faker->unique()->foodRice();
                                break;
                            case 6:
                                $dish = $faker->unique()->foodFruit();
                                break;
                        }
                    }

                    Dish::create([ // Insert new dish into database
                        'restaurant_id' => $restaurant->id,
                        'name' => $dish,
                        'desc' => $fakerDesc->text(),
                        'price' => rand(500, 6000) / 100,
                        'promo' => $promo[rand(0, 6)],
                        'category_id' => $category->id,
                        'pfp' => 'dish_img/default.png',
                        'created_at' => time(),
                        'updated_at' => time()
                    ]); 
                }       
            }
        }
    }
}
