<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Support\Facades\Auth;
use App\Models\Photo;

class DeleteDishImage implements InvokableRule
{
    /**
     * User cannot remove all dish's image at once
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        $num = Photo::find($value[0])->uploads->photos->count();
        if (count($value) == $num) {
            $fail("Remove all images at once is not allowed.");
        }
    }
}
