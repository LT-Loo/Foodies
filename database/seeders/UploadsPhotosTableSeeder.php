<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\Dish;
use App\Models\Upload;
use App\Models\Photo;


class UploadsPhotosTableSeeder extends Seeder
{
    /**
     * Save default dish photo for each dish in database
     *
     * @return void
     */
    public function run()
    {
        $dishes = Dish::all();

        foreach ($dishes as $dish) {
            $upload = Upload::create([
                'user_id' => $dish->restaurant_id,
                'dish_id' => $dish->id
            ]);

            Photo::create([
                'img' => $dish->pfp,
                'upload_id' => $upload->id
            ]);
        }
    }
}
