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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::match(['get','post'],'/','IndexController@index');
Route::get('products/{id}','ProductsController@products');
Route::get('categories/{id}','IndexController@categories');
Route::get('/get-product-price','ProductsController@getprice');
//Route for add to cart
Route::match(['get','post'], '/add-cart','ProductsController@addtocart');
// Route for Cart   
Route::match(['get','post'], '/cart','ProductsController@cart');
//Route for Delete Cart
Route::get('/cart/delete-product/{id}','ProductsController@deleteCartProduct');
//Route For update Quantity
Route::get('/cart/update-quantity/{id}/{quantity}','ProductsController@updateCartQuantity');

Route::match(['post','get'],'/admin','AdminController@login');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' =>['auth']], function(){
    Route::match(['post','get'],'/admin/dashboard','AdminController@dashboard');

    //Category Route
    Route::match(['post','get'],'/admin/add-category','CategoryController@addCategory');
    Route::match(['post','get'],'/admin/view-categories','CategoryController@viewCategories');
    Route::match(['post','get'],'/admin/edit-category/{id}','CategoryController@editCategory');
    Route::match(['post','get'],'/admin/delete-category/{id}','CategoryController@deleteCategory');
    Route::post('/admin/update-category-status','CategoryController@updateStatus');
    
    //Product Route
    Route::match(['post','get'],'/admin/add-product','ProductsController@addProduct');
    Route::match(['post','get'],'/admin/view-products','ProductsController@viewProducts');
    Route::match(['post','get'],'/admin/edit-products/{id}','ProductsController@editProducts');
    Route::match(['post','get'],'/admin/delete-products/{id}','ProductsController@deleteProducts');
    Route::post('/admin/update-product-status','ProductsController@updateStatus');
    Route::post('/admin/update-featured-product-status','ProductsController@updateFeatured');

    //Product Attributes

    Route::match(['post','get'],'/admin/add-attributes/{id}','ProductsController@addAttributes');
    Route::get('/admin/delete-attribute/{id}', 'ProductsController@deleteAttribute');
    Route::match(['get','post'],'/admin/edit-attributes/{id}','ProductsController@editAttributes');
    Route::match(['get','post'],'/admin/add-images/{id}','ProductsController@addImages');
    Route::get('/admin/delete-alt-image/{id}','ProductsController@deleteAltImage');

    // Banners Route
    Route::match(['post','get'],'/admin/banners','BannersController@banners');
    Route::match(['get','post'],'/admin/add-banner','BannersController@addBanner');
    Route::match(['get','post'],'/admin/edit-banner/{id}','BannersController@editBanner');
    Route::match(['get','post'],'/admin/delete-banner/{id}','BannersController@deleteBanner');
    Route::post('/admin/update-banner-status','BannersController@updateStatus');
    
});
Route::get('/logout','AdminController@logout');
