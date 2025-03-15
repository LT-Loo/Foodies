<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Rules\DuplicateCategory;
use App\Rules\DuplicateDish;
use App\Rules\DeleteDishImage;
use App\Models\User;
use App\Models\Dish;
use App\Models\Purchase;
use App\Models\Order;
use App\Models\Address;
use App\Models\Favourite;
use App\Models\Photo;
use App\Models\Category;
use App\Models\Upload;

class RestaurantController extends Controller
{
    // All routes require user login except for home page, restaurant page and dish page
    public function __construct() {
        $this->middleware('auth', ['except' => ['show', 'dish']]);
    }

    /**
     * Once restaurant logins, direct to restaurant page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $restaurant = User::find($user->id);
        $favourites = [];

        return view('foodies.restaurant')->with("user", $user)->with("restaurant", $restaurant)->with('favourites', $favourites)->with("cart", getCart());
    }

    /**
     * Create new dish
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Input validation
        $request->validate([
            'name' => ['required', 'string', 'min:4', 'max:30', new DuplicateDish],
            'price' => 'required|numeric|gt:0',
            'promo' => 'nullable|numeric|gte:0',
            'new_category' => ['nullable', 'required_without:category', 'string', new DuplicateCategory],
            'description' => 'nullable|string|max:400',
            'images' => 'required',
            'images.*' => 'image'
        ], [
            'name.max' => 'Dish name must not exceed 30 characters.',
            'price.gt' => 'The price must be at least $1',
            'promo.numeirc' => 'The promotion field must be a number.',
            'promo.gte' => 'The discount must be a positive number',
            'new_category.required_without' => 'This field is required',
            'images.required' => 'At least one file is required.',
            'images.*.image' => 'Only accept image files.',
            'desc.max' => 'Description must not exceed 400 characters'
        ]);

        // Get existing category ID or create new category
        $user = Auth::user();
        if ($request->category == null && $request->new_category) {
            $category = Category::create(["restaurant_id" => $user->id, "name" => $request->new_category, "order" => count($user->categories) + 1]);
            $category_id = $category->id;
        } else {$category_id = $request->category;}

        // Create new dish
        $dish = Dish::create([
            "name" => $request->name,
            "restaurant_id" => $user->id,
            "price" => $request->price,
            "promo" => $request->promo,
            "category_id" => $category_id,
            "desc" => $request->desc
        ]);

        // Store images
        $images = $request->images;
        if ($images) {
            foreach ($images as $key => $image) {
                $img_path = $image->store('dish_img', 'public');
                if ($key == 0) {
                    $dish->pfp = $img_path;
                    $dish->save();
                    $upload = Upload::create(["user_id" => $user->id, "dish_id" => $dish->id]);            
                }
                Photo::create(["img" => $img_path, "upload_id" => $upload->id]);
            }
        }

        return redirect("restaurant");
    }

    /**
     * Show selected restaurant page
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = getUser();
        $restaurant = User::find($id);
        $favourites = [];
        foreach ($user->favourites as $fave) {;
            if ($fave->restaurant_id == $id) {
                $favourites[] = $fave->id;
            }
        }
        
        return view('foodies.restaurant')->with("user", $user)->with("restaurant", $restaurant)->with('favourites', $favourites)->with("cart", getCart());
    }
    
    // Show selected dish detail page
    public function dish($id) {

        $user = getUser();
        $dish = Dish::find($id);
        $fave = Favourite::whereRaw("customer_id = ? and dish_id = ?", [$user->id, $id])->exists();
        $reviews = $dish->reviews()->whereRaw("user_id != ?", [$dish->restaurant_id])->orderBy('created_at', 'desc')->get();
        $img = $dish->reviews()->whereRaw("user_id = ?", [$dish->restaurant_id])->get();
        if (count($img) > 0) {$dish->images = $img[0]->photos;}
        else {$dish->images = [];}
        
        return view("foodies.dish")->with("user", $user)->with("dish", $dish)->with("reviews", $reviews)->with("fave", $fave)->with("cart", getCart());
    }

    /**
     * Edit selected dish details
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $dish = Dish::find($id);

        // Input validation
        $request->validate([
            'price' => 'required|numeric|gt:0',
            'promo' => 'nullable|numeric|gte:0',
            'new_category' => ['nullable', 'required_without:category', 'string', new DuplicateCategory],
            'description' => 'nullable|string|max:400',
            'delete_images' => [new DeleteDishImage],
            'images.*' => 'image'
        ], [
            'price.gt' => 'The price must be at least $1',
            'promo.numeirc' => 'The promotion field must be a number.',
            'promo.gte' => 'The discount must be a positive number',
            'new_category.required_without' => 'This field is required',
            'images.*.image' => 'Only accept image files.',
            'desc.max' => 'Description must not exceed 400 characters'
        ]);

        // Remove image if requested
        $deletes = $request->delete_images;
        if ($deletes) {
            foreach ($deletes as $image) {
                $del = Photo::find($image);
                if ($del->img == $dish->pfp) { // If image deleted is pfp, set path to null
                    $dish->pfp = null;
                }
                $del->delete();
            }
        }

        // Store new images if provided
        $upload = Upload::whereRaw("dish_id = ? and user_id = ?", [$dish->id, $dish->restaurant_id])->first();
        $images = $request->images;
        if ($images) {
            foreach ($images as $key => $image) {
                $img_path = $image->store("dish_img", "public");
                Photo::create(["img" => $img_path, "upload_id" => $upload->id]);
                if ($key == 0 && $dish->pfp == null) {
                    $dish->pfp = $img_path;
                }
            }
        } 

        // Get existing category ID or create new category
        $user = Auth::user();
        if ($request->category == null && $request->new_category) {
            $category = Category::create(["restaurant_id" => $user->id, "name" => $request->new_category, "order" => count($user->categories) + 1]);
            $category_id = $category->id;
        } else {$category_id = $request->category;}

        // Update details
        $dish->price = $request->price;
        $dish->promo = $request->promo;
        $dish->category_id = request("category");
        $dish->desc = $request->desc;
        $dish->save();

        return back();
    }

    /**
     * Remove selected dish from menu
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $dish = Dish::find($id);
        $restaurant = $dish->restaurant_id;
        $dish->delete();

        return redirect("restaurant");
    }

    // Display customer orders
    public function orders() {
        $user = Auth::user();
        $purchases = $user->rest_purchases->sortByDesc("created_at");

        // Calculate total price for each order
        foreach ($purchases as $purchase) {
            $sum = 0;
            foreach ($purchase->dishes as $dish) {
                $sum += ceil($dish->pivot->price * (100 - $dish->pivot->promo) * $dish->pivot->quantity) / 100;
            }
            $purchase->total = $sum; 
        }
    
        return view('foodies.order')->with("user", $user)->with("purchases", $purchases)->with("cart", getCart());
    }

}
