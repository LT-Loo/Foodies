<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Support\Facades\Auth;

class DuplicateDish implements InvokableRule
{
    /**
     * Check for duplicate dish in menu
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        $user = Auth::user();
        $dishes = $user->dishes;
        $dish_names = array_map("strtolower", $dishes->pluck("name")->toArray());
        if (in_array(strtolower($value), $dish_names)) {
            $fail("Dish already exists.");
        } 
    }
}
