<?php

use App\Models\User;

function getCart() {
    if (!session()->has("cart")) {
        session(["cart" => [
            "empty" => true,
            "restaurant" => null,
            "dishes" => null
        ]]);
    }
    return session("cart");
}

function clearCart() {
    session(["cart" => [
        "empty" => true,
        "restaurant" => null,
        "dishes" => null
    ]]);
}

function cartTotal() {
    $dishes = session("cart")["dishes"];
    $total = 0;
    foreach ($dishes as $dish) {
        $total += ceil($dish->price * (100 - $dish->promo) * $dish->quantity) / 100;
    }

    return $total;
}

function getUser() {
    if (Auth::check()) {
        return Auth::user();
    } else  {return User::find(2);}
}