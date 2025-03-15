<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\RestaurantController;
use App\Models\User;
use App\Models\Dish;
use App\Models\Purchase;
use App\Models\Order;
use App\Models\Address;
use App\Models\Favourite;
use App\Models\Photo;
use App\Models\Upload;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Customer Controller
Route::post('order-or-cart', [CustomerController::class, 'orderOrCart']);
Route::get('order-from-cart', [CustomerController::class, 'orderFromCart']);
Route::post('new-address', [CustomerController::class, 'newAddress']);
Route::post('select-address', [CustomerController::class, 'selectAddress']);
Route::get('add-cart', [CustomerController::class, 'orderToCart']);
Route::get('remove-dish/{id}', [CustomerController::class, 'removeDish']);
Route::get('empty-cart', [CustomerController::class, 'emptyCart']);
Route::get('purchase', [CustomerController::class, 'purchase']);
Route::get('order-history', [CustomerController::class, 'history']);
Route::get('favourite', [CustomerController::class, 'favourite']);
Route::get('like/{id}', [CustomerController::class, 'like']);
Route::get('dislike/{id}', [CustomerController::class, 'dislike']);
Route::post('post-review/{id}', [CustomerController::class, 'postReview']);
Route::resource('customer', CustomerController::class);

// Restaurant Controller
Route::get('dish/{id}', [RestaurantController::class, 'dish']);
Route::get('orders', [RestaurantController::class, 'orders']);
Route::resource('restaurant', RestaurantController::class);

// Home Page
Route::get('/', function () {

    $restaurants = User::whereRaw("userType = 2 or userType = 3")->paginate(6); // Get all approved restaurants

    // Generate recommendation list for customer
    if (Auth::check() && Auth::user()->userType == 1) {
        $favourites = Auth::user()->favourites;
        $dishes = Dish::all();

        $dish_points = [];
        if (count($favourites) >= 5) {
            $restTypes = [];
            $dishCategories = [];

            // Set points for restaurant and category type
            foreach ($favourites as $favourite) {
                $type = strtolower($favourite->restaurant->restType);
                $category = strtolower($favourite->category->name);

                if (!array_key_exists($type, $restTypes)) {$restTypes[$type] = 1;} 
                else {$restTypes[$type] += 1;}
                
                if (!array_key_exists($category, $dishCategories)) {$dishCategories[$category] = 1;} 
                else {$dishCategories[$category] += 1;}
            }

            // Calculate points for each dish
            foreach ($dishes as $dish) {
                $point = 0;
                $valid = true;

                // Give points if restaurant type matches
                if (array_key_exists(strtolower($dish->restaurant->restType), $restTypes)) {
                    $point += $restTypes[strtolower($dish->restaurant->restType)];
                }

                // Give points if category type matches
                if (array_key_exists(strtolower($dish->category->name), $dishCategories)) {
                    $point += $dishCategories[strtolower($dish->category->name)] * 0.5;
                }

                // Give points according to match words in dish name and description
                foreach ($favourites as $favourite) {
                    if ($favourite->id == $dish->id) { // Skip favourite dish
                        $valid = false;
                        break;
                    } 
                    else {
                        // Dish Name Comparison
                        $faveName = explode(" ", strtolower($favourite->name));
                        foreach ($faveName as $word) {
                            $point += substr_count(strtolower($dish->name), $word);
                        }

                        // Dish Description Comparison
                        $words = [];
                        $faveDesc = explode(" ", strtolower($favourite->desc));
                        foreach ($faveDesc as $word) {
                            if (strlen($word) > 3 && !in_array($word, $words)) {
                                $point += substr_count(strtolower($dish->desc), $word) * 0.5;
                                $words[] = $word;
                            }
                        }
                    }
                }

                if ($dish->promo > 0) {$point += $dish->promo / 100;}

                if ($valid) { // Record points
                    $dish->point = $point;
                    $dish_points[] = $dish;
                }
            }

            // Sort dishes based on points 
            array_multisort(array_column($dish_points, 'point'), SORT_DESC, $dish_points);

            $recommend = array_slice($dish_points, 0, 5, true); // Get Top 5
        } else {$recommend = [];}
    } else {$recommend = [];}

    return view('foodies.home')->with("restaurants", $restaurants)->with("recommend", $recommend)->with("cart", getCart());
});

// Top 5 Popular Dishes
Route::get('/top-5', function () {

    // Get Top 5 Dish
    $date = new DateTime("-30 days");
    $topOrders = Order::selectRaw('*, count(id) as count')->whereRaw("created_at >= ?", [$date])->groupBy('dish_id')->orderBy('count', 'desc')->limit(5)->get();

    // Get user
    if (Auth::check()) {
        $user = Auth::user();
    } else  {$user = User::find(2);}

    // Check if top 5 dish matches with favourite dish
    $favourites = [];
    $top5 = [];
    foreach ($topOrders as $dish) {
        $d = $dish->dish;
        $d->count = $dish->count;
        $top5[] = $d;
        foreach ($user->favourites as $fave) {
            if ($fave->id == $dish->dish_id) {
                $favourites[] = $dish->dish_id;
            }
        }
    }
    
    return view('foodies.top-5')->with("user", $user)->with('top5', $top5)->with('favourites', $favourites)->with("cart", getCart());
});


// New restaurant list (Admin only)
Route::get('/new-restaurants', function() {
    $user = Auth::user();
    $restaurants = User::whereRaw("userType = 3")->get();
    return view("foodies.new-restaurants")->with("restaurants", $restaurants)->with("user", $user)->with("cart", getCart());
});

// Approve action (Admin only)
Route::get('/approve/{id}', function ($id) {
    $restaurant = User::find($id);
    $restaurant->userType = 2;
    $restaurant->save();

    return back();
});

// Display documentation of project
Route::get('/doc', function () {
    return view('foodies.doc')->with('user', getUser())->with('cart', getCart());
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
