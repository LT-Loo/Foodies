<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dishes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('restaurant_id');
            $table->foreign('restaurant_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name');
            $table->integer('category_id');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->text('desc')->nullable();
            $table->float('price');
            $table->integer('promo')->nullable()->default(0);
            $table->string('pfp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('dishes');
        Schema::enableForeignKeyConstraints();
    }
};
