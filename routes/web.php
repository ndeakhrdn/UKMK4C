<?php

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

use Illuminate\Http\Request;

Auth::routes();

/*---------------------- CUSTOM LOGIN & REGISTER -------------------------------*/
Route::post('/login/custom', 'LoginController@login')->name('login.custom');

Route::group(['middleware' => ['auth','admin']], function(){
	Route::post('register','Auth\RegisterController@register');
	Route::get('register','Auth\RegisterController@showRegistrationForm')->name('register');
});

/*------------------------ CUSTOMER -------------------------------*/

Route::group(['prefix'=>'/','as'=>'cust.', 'name'=>'cust' ], function(){

	Route::get('/','CustomerController@index')->name('index');
	// Route::get('/{id}','CustomerController@category')->name('category');
	Route::get('/home','CustomerController@index')->name('home');

	Route::get('add-to-cart/{product_id}', 'CustomerController@AddToCart')->name('addcart');
	Route::get('/cart', 'CustomerController@getCart')->name('getcart');

	/*----------------------------- AUTHENTICATE --------------------------*/
	Route::group(['middleware' => 'auth'], function(){
		Route::get('checkout/{user}', 'CustomerController@checkout')->name('checkout');

		Route::put('isNotified/{id}','NotifyController@isNotified')->name('isNotified');
		Route::put('isNotifiedAll/{id}','NotifyController@isNotifiedAll')->name('isNotifiedAll');
		Route::get('refreshNavbar','NotifyController@refreshNavbar')->name('refreshNavbar');

		Route::group(['prefix'=>'/profile', 'name'=>'profile', 'as'=>'profile.'], function(){
			Route::get('create', 'ProfileController@create')->name('create');
			Route::post('store', 'ProfileController@store')->name('store');
			Route::get('edit/{id}', 'ProfileController@edit')->name('edit');
			Route::get('','ProfileController@index')->name('index');
			Route::get('show/{user}', 'ProfileController@show')->name('show');
			Route::put('update/{user}','ProfileController@update')->name('update');
		});
	});
});

/*---------------------- ADMIN ----------------------------*/

Route::group(['prefix'=>'staff', 'as'=>'staff.', 'middleware' => ['auth','admin'], 'name'=>'staff' ], function(){
	Route::get('/', 'OrderController@index')->name('index');
	Route::get('register','Auth\StaffRegisterController@showRegistrationForm')->name('register');
	Route::post('register','Auth\StaffRegisterController@register');
	Route::get('viewfeedback',  'StaffController@viewFeedback')->name('viewfeedback');
	Route::get('report',  'StaffController@report')->name('report');

Route::group(['prefix' => 'customer','middleware' => ['auth','admin'], 'as'=>'customer.','name'=>'customer'], function(){
	Route::get('/','AjaxController@index')->name('index');
});   

	/*------------------------------------ ADVERTISEMENT ----------------------------------*/
	Route::group(['prefix' => 'advertisement', 'as'=>'advertisement.','name'=>'advertisement'], function(){
		Route::get('/','AdverController@index')->name('index');
		Route::get('create', 'AdverController@create')->name('create');
		Route::post('store', 'AdverController@store')->name('store');
		Route::get('{id}', 'AdverController@edit')->name('edit');
		Route::put('update/{id}','AdverController@update')->name('update');
		Route::delete('delete/{id}', 'AdverController@destroy')->name('delete');
	});



	/*------------------------------------ PRODUCT MANAGEMENT ----------------------------------*/
	Route::group(['prefix' => 'product','as'=>'product.','name'=>'product'],function(){
		Route::get('/', 'ProductController@index')->name('index');
		Route::get('{product_id?}', 'ProductController@show')->name('show');
		Route::post('/', 'ProductController@create')->name('create');
		Route::put('{product_id?}', 'ProductController@update')->name('update');
		Route::delete('{product_id?}', 'ProductController@destroy')->name('delete');
		Route::post('upload', 'ProductController@upload')->name('upload');
	});

	/*------------------------------------ ORDER MANAGEMENT ----------------------------------*/
	Route::group(['prefix'=>'order', 'name'=>'order', 'as'=>'order.' ], function(){
		Route::get('/', 'OrderController@index')->name('index');
		Route::get('update/{id}/{cust}','OrderController@update')->name('update');
	});

	/*------------------------------------ TOPUP ----------------------------------*/
	Route::group(['prefix'=>'topup', 'as' => 'topup.', 'name' => 'topup'], function(){

		Route::get('/', function(){
			return view('staff.topup.index');
		});
		Route::get('{cust_id?}',function($cust_id){
			$customer = App\Customer::find($cust_id);
			return response()->json($customer);
		});
		Route::put('{cust_id?}', function(Request $request,$cust_id){
			$customer = App\Customer::find($cust_id);
			$customer->cust_balance += $request->cust_balance;
			$customer->save();
			return response()->json($customer);
		});
	});
});

/*--------------------------------------------------------------------------------------------*/

Route::get('orderhistory', [
	'uses' => 'CustomerController@orderHistory',
	'as' => 'customer.orderHistory'
]);

Route::get('orderhistory/{product_id}',function($product_id){
	$rating = App\Rating::where('product_id', $product_id)->get();
	return response()->json($r);
});

Route::post('orderhistory/{order_id}/{product_id}', [
	'uses' => 'CustomerController@sendRating',
	'as' => 'customer.sendRating'
]);

Route::group(['prefix'=>'/orderhistory/', 'as'=>'customer.', 'name'=>'customer' ], function(){
	Route::put('sendFeedback/{id}','CustomerController@sendFeedback')->name('sendFeedback');
});