<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Address;

class AddressesTableSeeder extends Seeder
{
    /**
     * Insert address for each customer
     *
     * @return void
     */
    public function run()
    {
        $customers = User::whereRaw('userType = 1')->get();

        foreach ($customers as $customer) {
            Address::create([
                'customer_id' => $customer->id,
                'address' => $customer->address,
                'default' => true,
                'created_at' => $customer->created_at,
                'updated_at' => $customer->updated_at
            ]);
        }
    }
}
