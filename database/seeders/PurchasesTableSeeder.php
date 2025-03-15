<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Purchase;

class PurchasesTableSeeder extends Seeder
{
    /**
     * Create purchase record for each customer
     *
     * @return void
     */
    public function run()
    {
        $restaurants = User::select('id')->whereRaw("userType = 2")->get();
        $customers = User::select('id', 'address')->whereRaw("userType = 1")->get();

        foreach ($customers as $customer) {
            Purchase::create([
                'customer_id' => $customer->id,
                'restaurant_id' => $restaurants[rand(0, count($restaurants) - 1)]->id,
                'address' => $customer->address,
                'created_at' => time()
            ]);
        }
    }
}
