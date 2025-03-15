<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $this->call(UsersTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(DishesTableSeeder::class);
        $this->call(UploadsPhotosTableSeeder::Class);
        $this->call(PurchasesTableSeeder::class);
        $this->call(OrdersTableSeeder::class);
        $this->call(AddressesTableSeeder::class);
        $this->call(FavouritesTableSeeder::class);
        
    }
}
