<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create($registerType)
    {   
        return view('auth.register')->with('registerType', $registerType);
    }

    public function registerType(Request $request)
    {   
        return redirect("register/$request->registerType");
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, $registerType)
    {   
        // Input validation
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::default()],
            'address' => 'required|string|max:255',
            'restType' => 'max:255',
            'desc' => 'max:400',
            'pfp' => 'required_unless:registerType,restaurant|image'
        ], [
            'restType.max' => 'The restaurant type must not be greater than 255 characters.',
            'desc.max' => 'The description must not be greater than 400 characters.',
            'pfp.required_unless' => 'Profile picture is required.',
            'pfp.image' => 'File uploaded must be an image.'
        ]);

        // Determine type of member
        if ($registerType == "customer") {
            $userType = 1;
            $img_path = null;
        }
        else {
            $userType = 3;
            $img_path = request()->file("pfp")->store("restaurant_pfp", "public");
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'userType' => $userType,
            'desc' => $request->desc,
            'restType' => $request->restType,
            'pfp' => $img_path
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }

}
