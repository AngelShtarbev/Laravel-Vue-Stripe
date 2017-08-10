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

Route::get('/', function () {
    $plans = App\Plan::all();
    return view('welcome',compact('plans'));
});

Route::post('subscriptions', 'SubscriptionsController@store');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::post('stripe/webhook', 'WebhooksController@handle');

Route::delete('subscriptions', 'SubscriptionsController@destroy');

Route::patch('subscriptions', 'SubscriptionsController@update');

Route::get('products/{product}', 'ProductsController@show');
