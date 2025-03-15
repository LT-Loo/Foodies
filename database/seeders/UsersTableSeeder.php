<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Generate users with different user types
     *
     * @return void
     */
    public function run()
    {
        User::create([ // Admin 
            'name' => 'admin',
            'email' => 'admin@example.com',
            'email_verified_at' => time(),
            'password' => bcrypt('admin'),
            'address' => 'Griffith University (Gold Coast Campus)',
            'userType' => 0,
            'created_at' => time(),
            'updated_at' => time()
        ]);

        User::create([ // Guest (User not logged in)
            'name' => 'guest',
            'email' => 'guest@example.com',
            'email_verified_at' => time(),
            'password' => bcrypt('password'),
            'address' => fake()->address(),
            'userType' => -1,
            'created_at' => time(),
            'updated_at' => time()
        ]);

        $type = ['Asian', 'Fast Food', 'Mexican', 'Indian', 'Japanese', 'Western', 'Chinese', 'Italian', 'Thai', 'Vietname'];
        for ($i = 1; $i <= 20; $i++) {
            if ($i <= 5) { // Customers
                $name = "User$i";
                $email = "user$i@example.com";
                $desc = null;
                $restType = null;
                $userType = 1;
            }
            else { // Restaurants (Approved/Waiting to be approved)
                $userType = rand(2, 3);
                $name = "Restaurant$i";
                $email = "restaurant$i@example.com";
                $desc = fake()->text();
                $restType = $type[array_rand($type)];
            }

            User::create([
                'name' => $name,
                'email' => $email,
                'email_verified_at' => time(),
                'password' => bcrypt('password'),
                'address' => fake()->address(),
                'userType' => $userType,
                'desc' => $desc,
                'restType' => $restType,
                'pfp' => "restaurant_pfp/default.png",
                'created_at' => time(),
                'updated_at' => time()
            ]);
        }
    }
}
