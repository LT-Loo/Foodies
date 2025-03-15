<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\Dish;
use App\Models\Purchase;
use App\Models\Order;

class OrdersTableSeeder extends Seeder
{
    /**
     * Create order for each purchase
     *
     * @return void
     */
    public function run()
    {
        $purchases = Purchase::select('id', 'restaurant_id')->get();
        
        foreach ($purchases as $purchase) {
            $dishes = Dish::whereRaw('restaurant_id = ?', [$purchase->restaurant_id])->get();
            $dish = $dishes[rand(0, count($dishes) - 1)];
            Order::create([
                'purchase_id' => $purchase->id,
                'dish_id' => $dish->id,
                'quantity' => rand(1, 5),
                'price' => $dish->price,
                'promo' => $dish->promo,
                'created_at' => time()
            ]);
        }
    }
}
