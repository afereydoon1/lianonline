<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', 'Front\UserController@register');
Route::post('/validate-otp', 'Front\UserController@validateOtp');
Route::post('/login', 'Front\UserController@login');

Route::post('/search','Front\HomeController@search');

Route::post('/get-site-info','Front\HomeController@getSiteInfo');

Route::post('/get-products','Front\ProductController@getProducts');
Route::post('/get-product/{id}','Front\ProductController@getProduct');
Route::post('/get-product-comments','Front\ProductController@getProductComments');
Route::post('/get-user-product-comments','Front\ProductController@getUserProductsComments');

Route::post('/get-posts','Front\PostController@getPosts');
Route::post('/get-post/{id}','Front\PostController@getPost');
Route::post('/get-post-comments','Front\PostController@getPostComments');
Route::post('/get-user-post-comments','Front\PostController@getUserPostsComments');

Route::post('/get-categories','Front\CategoryController@getCategories');
Route::post('/get-tags','Front\TagController@getTags');
Route::post('/get-user-info','Front\UserController@getUserInfo');
Route::post('/get-user-stats','Front\UserController@getUserStats');

Route::group(['middleware' => 'auth:user-api', 'namespace' => 'Front'], function() {
    Route::post('/get-my-info','UserController@getMyInfo');
    Route::post('/set-user-info','UserController@setUserInfo');
    Route::post('/change-password','UserController@changePassword');
    Route::post('/get-sales-list','UserController@getSalesList');
    Route::post('/get-orders-list','UserController@getOrdersList');
    Route::post('/get-transactions-list','UserController@getTransactions');
    Route::post('/set-withdraw-request','UserController@setWithdrawRequest');
    Route::post('/get-referral-list','UserController@getReferralRequests');

    Route::post('/create-referral','HomeController@createReferral');

    Route::post('upload-file','UploadController@uploadFile');
    Route::post('upload-image','UploadController@uploadImage');
    // Products
    Route::post('/product/list', 'ProductController@index');
    Route::post('/product/create', 'ProductController@create');
    Route::post('/product/show', 'ProductController@show');
    Route::post('/product/edit', 'ProductController@edit');
    Route::post('/product/delete', 'ProductController@delete');

    Route::post('/product/get-categories/{id?}', 'ProductController@getCategories');
    Route::post('/product/get-tags', 'ProductController@getTags');

    Route::post('/set-product-comment','ProductController@setProductComments');

    // Posts
    Route::post('/post/list', 'PostController@index');
    Route::post('/post/create', 'PostController@create');
    Route::post('/post/show', 'PostController@show');
    Route::post('/post/edit', 'PostController@edit');
    Route::post('/post/delete', 'PostController@delete');
    Route::post('/post/upload/image', 'PostController@upload');

    Route::post('/post/get-categories', 'PostController@getAssortments');
    Route::post('/post/get-tags', 'PostController@getTags');

    Route::post('/set-post-comment','PostController@setPostComments');

    // Tickets
    Route::post('/create-ticket', 'TicketController@createTicket');
    Route::post('/reply-ticket', 'TicketController@replyTicket');
    Route::post('/get-ticket', 'TicketController@getTicket');
    Route::post('/get-tickets', 'TicketController@getTickets');
    Route::post('/set-contacts', 'TicketController@setContacts');
    Route::post('/get-contacts', 'TicketController@getContacts');

    // Orders
    Route::post('/get-order-info', 'OrderController@getOrderInfo');
    Route::post('/create-order', 'OrderController@createOrder');
});
