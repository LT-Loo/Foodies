<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Dish;
use App\Models\Purchase;
use App\Models\Order;
use App\Models\Address;
use App\Models\Favourite;
use App\Models\Photo;
use App\Models\Upload;

class CustomerController extends Controller
{
    // All routes require user login
    public function __construct() {
        $this->middleware('auth');
    }


    /**
     * Show order details
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        $order = session("order");

        return view('foodies.confirm-purchase')->with('user', $user)->with("order", $order)->with('confirm', false)->with("cart", getCart());
    }

    /**
     * Order dish immediately or add dish to cart
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function orderOrCart(Request $request)
    {
        // Input validation
        $request->validate(['quantity' => 'required|integer']);

        // Get dish details
        $dish = Dish::find($request->dish);
        $dish->quantity = $request->quantity;

        switch ($request->action) {
            // Order now - Add dish to order session
            case 'order':
                $total = (ceil($dish->price * (100 - $dish->promo) * $dish->quantity)) / 100;
                session(["order" => [
                    "restaurant" => $dish->restaurant,
                    "dishes" => [$dish],
                    "address" => Address::whereRaw('customer_id = ? and `default` = true', [Auth::user()->id])->first(),
                    "total" => $total
                ]]); 
                
                return redirect('customer/create'); // Redirect to order confirmation page

            // Add to cart
            case 'cart' :
                // If cart empty, get restaurant details
                if (session("cart.empty")) {
                    session(["cart.empty" => false]);
                    session(["cart.restaurant" => $dish->restaurant]);
                    session(["cart.dishes" => []]);
                }

                // If dish from same restaurant
                if (session("cart.restaurant")->id == $dish->restaurant_id) {

                    $cart = session("cart.dishes");
  
                    // Return error if dish duplicates
                    if (in_array($dish, $cart)) {
                        return back()->with("duplicate", $dish->name)->with("show-cart", true);
                    }

                    // Add dish into cart
                    $cart[] = $dish;
                    session(["cart.dishes" => $cart]);

                    return redirect("dish/$dish->id")->with("show-cart", true); 

                } else { // If dish from different restaurant, return error
                    return back()->with("error", session("cart.restaurant")->name);
                }   

                break;
        }
    }

    // Place order from cart
    public function orderFromCart() {
        $cart = session("cart");

        $total = cartTotal(); // Calculate total price

        // Store order details in order session
        session(["order" => [
            "restaurant" => $cart["restaurant"],
            "dishes" => $cart["dishes"],
            "address" => Address::whereRaw('customer_id = ? and `default` = true', [Auth::user()->id])->first(),
            "total" => $total
        ]]);  

        clearCart();

        return redirect('customer/create'); // Redirect to order confirmation page
    }

    // Confirm order 
    public function purchase(Request $request)
    {
        $user = Auth::user();
        $order = session("order");

        // Store purchase details in database
        $purchase = Purchase::create(["customer_id" => $user->id, "restaurant_id" => $order["restaurant"]->id, "address" => $order["address"]->address]);
        foreach ($order["dishes"] as $dish) {
            Order::create(["purchase_id" => $purchase->id, "dish_id" => $dish->id, "quantity" => $dish->quantity, "price" => $dish->price, "promo" => $dish->promo]);
        }

        clearCart(); // Empty cart

        return view('foodies.confirm-purchase')->with('user', $user)->with("order", $order)->with('confirm', true)->with("cart", getCart());
    }

    // Favourite List
    public function favourite() {
        $favourites = Auth::user()->favourites()->paginate(6);
        return view("foodies.favourite")->with("user", Auth::user())->with("favourites", $favourites)->with("cart", getCart());
    }

    // Order history
    public function history() {
        $user = Auth::user();
        $purchases = $user->cust_purchases->sortByDesc("created_at");

        // Calculate total price for each purchase
        foreach ($purchases as $purchase) {
            $sum = 0;
            foreach ($purchase->dishes as $dish) {
                $sum += ceil($dish->pivot->price * (100 - $dish->pivot->promo) * $dish->pivot->quantity) / 100;
            }
            $purchase->total = $sum; 
        }
    
        return view('foodies.order')->with("user", $user)->with("purchases", $purchases)->with("cart", getCart());
    }

    // Add new delivery address
    public function newAddress(Request $request) {

        // Input validation
        $request->validate(['address' => 'required']);

        // Set default
        $user = Auth::user();
        if (isset($_POST["default"])) {
            $default = true;
            $default_address = Address::whereRaw("customer_id = ? and `default` = ?", [$user->id, true])->first();
            $default_address->default = false;
            $default_address->save();
        }
        else {$default = false;}

        // Set new address as order's delivery address
        $selected = Address::create(["customer_id" => $user->id, "address" => $request->address, "default" => $default]);
        $order = session("order");
        $order["address"] = $selected;
        session(["order" => $order]);

        return redirect('customer/create');
    }

    // Change order's delivery address
    public function selectAddress(Request $request) {

        if (isset($_POST["address"])) {
            $order = session("order");
            $order["address"] = Address::find($_POST["address"]);
            session(["order" => $order]);
        }
    
        return redirect('customer/create');
    }

    // Place order into cart
    public function orderToCart() {
        $order = session("order");
        $restaurant = $order["restaurant"];

        if (session("cart.empty")) { // If cart empty, get restaurant details and add dish to cart
            session(["cart.empty" => false]);
            session(["cart.restaurant" => $order["restaurant"]]);
            session(["cart.dishes" => $order["dishes"]]);
        } else { // If cart not empty
            
            if (session("cart.restaurant")->id != $restaurant->id) { // Return error if dishes not from same restaurant
                return back()->with("error", session("cart.restaurant")->name);
            } else { // If cart not empty

                // Return error if dish duplicates
                if (in_array($order["dishes"][0], session("cart.dishes"))) {
                    return back()->with("duplicate", $order["dishes"][0]->name)->with("show-cart", true);
                }

                // Add order into cart
                $cart = session("cart.dishes");
                $cart = array_merge($cart, $order["dishes"]);
                session(["cart.dishes" => $cart]);              
            }    
        }

        return redirect("restaurant/$restaurant->id")->with("show-cart", true);
    }

    // Add dish to favourite list
    public function like($id) {

        // Check if favourite  dish duplicates
        if (Favourite::whereRaw("dish_id = ? and customer_id = ?", [$id, Auth::user()->id])->get()->isEmpty()) {
            Favourite::create(["customer_id" => Auth::user()->id, "dish_id" => $id]); 
         }

         return back();
    }

    // Remove dish from favourite list
    public function dislike($id) {
        $dish = Favourite::whereRaw("customer_id = ? and dish_id = ?", [Auth::user()->id, $id]);
        $dish->delete();

        return back();
    }

    // Empty cart
    public function emptyCart() {
        if (!session("cart.empty")) {clearCart();}
        return back()->with("show-cart", true);
    }

    // Remove dish from cart
    public function removeDish($id) {
        $dishes = session("cart")["dishes"];

        foreach ($dishes as $key => $dish) {
            if ($dish->id == $id) {unset($dishes[$key]);}
        }

        if (empty($dishes)) {clearCart();}
        else {session(["cart.dishes" => $dishes]);}
        
        return back()->with("show-cart", true);
    }

    // Post review to a dish
    public function postReview(Request $request, $id) {

        // Input validation
        $request->validate([
            'comment' => 'required_without_all:r_images,r_images.*| max: 255',
            'r_images' => 'required_without:comment',
            'r_images.*' => 'required_without:comment|image'
        ], [
            'comment.required_without_all' => 'Either one of these fields must not be blank.',
            'r_images.required_without' => 'Either one of these fields must not be blank.',
            'r_images.*.image' => 'Only image file accepted.'
        ]);

        // Get request data
        $user = Auth::user();
        $images = $request->r_images;

        // Create review and save photos (if provided)
        $review = Upload::create(["user_id" => $user->id, "dish_id" => $id, "comment" => $request->comment]);
        if ($images) {
            foreach ($images as $image) {
                $img_path = $image->store('dish_img', 'public');
                $photo = Photo::create([
                    "img" => $img_path,
                    "upload_id" => $review->id
                ]);
            }    
        }

        return back();
    }

}
