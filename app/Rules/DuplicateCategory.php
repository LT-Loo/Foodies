<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Support\Facades\Auth;

class DuplicateCategory implements InvokableRule
{
    /**
     * Check for dupicate category in menu
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        $user = Auth::user();
        $categories = $user->categories;
        $category_names = array_map("strtolower", $categories->pluck("name")->toArray());
        if (in_array(strtolower($value), $category_names)) {
            $fail("Category already exists.");
        } 
    }
}
