<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Cart;
use App\Order;
use App\Orderline;
use App\Customer;
use App\Category;
use App\Rating;
use Session;

class CustomerController extends Controller
{
    public function index()
    {
        //
        $product = Product::paginate(6);
        $category = Category::get();
        $rating = Rating::get();
        return view('customer.index',compact('product','category', 'rating'));
    }

    public function AddToCart(Request $request, $product_id){
        $product = Product::find($product_id);
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->add($product, $product->product_id);

        $request->session()->put('cart', $cart);
        // dd($request->session()->get('cart'));
        return redirect()->route('cust.index')->with('success','Item has been added to cart!');
    }

    public function getCart(){
        if (!Session::has('cart')){
            return view('customer.cart',['products' => null]);
        }
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        return view('customer.cart',['products'=>$cart->items, 'totalPrice'=>$cart->totalPrice]);
    }

    public function checkout(Request $request, $user){
        $customer = Customer::find($user); 
        $category = Category::get(); 

        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);

        $totalPrice=$cart->totalPrice;
        $balance = $customer->cust_balance;

        if($balance<$totalPrice){
            return redirect()->route('cust.index')->with('error','Sorry, you have insufficient balance. Please topup at the store.');
        }
        else{
            $balance -=  $cart->totalPrice;

            $order_id = 'OD'. substr(uniqid(),2,8);
            $order_status = 'In Progress';
            $order_date = date('d-m-y h:i:s A');

            Order::create([
                'order_id' => $order_id,
                'cust_id' => $user,
                'order_date' => $order_date,
                'order_status' => $order_status,
                'total_price' => $totalPrice
            ]);

            if (Session::has('cart')){
                foreach ($cart->items as $products) {
                    Orderline::create([
                    'order_id' => $order_id,
                    'product_id' => $products['item']['product_id'], 
                    'quantity' => $products['qty']
                    ]);
                }
            }
            
            Customer::findOrFail($user)->update([
                'cust_balance' => $balance
            ]);
            
            Session::forget('cart');
            return redirect()->route('cust.index')->with('success','Order has been successfully made!');
        }
    }
    public function sendRating(Request $request, $product_id){
        // $product = Product::find($product_id);
        Rating::create([
            'product_id' => $product_id,
            'product_rating' => $request->input(),
        ]);


        return redirect()->route('ratetest')->with('success','Thank you for rating!');
    }
}
