<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Management Api routes for your application. These
| routes are loaded by the RouteServiceProvider within a group . Now create something great!
|
*/
Route::group(['prefix' => '/management/api/'], function () {
    Route::post('/auth/login', 'Auth\AuthController@login');
    Route::post('/auth/forget/send', 'Auth\ForgetPasswordController@send');
    Route::post('/auth/forget/reset', 'Auth\ForgetPasswordController@reset');
});

Route::group(['middleware' => ['auth:admin-api'], 'prefix' => '/management/api/'], function () {
    Route::post('/auth/logout', 'Auth\AuthController@logout');
    Route::post('/auth/check', 'Auth\AuthController@me');

    Route::post('/getDashboard', 'SettingController@getDashbordInfo');
    Route::post('/settings/general', 'SettingController@getGeneralSetting');
    Route::post('/settings/general/save', 'SettingController@setGeneralSetting');

    Route::post('/settings/sms', 'SettingController@getSmsSetting');
    Route::post('/settings/sms/save', 'SettingController@setSmsSetting');

    Route::post('/settings/gateway', 'SettingController@getGatewaySetting');
    Route::post('/settings/gateway/save', 'SettingController@setGatewaySetting');


    Route::post('/settings/slides/list', 'SettingController@slidesList');
    Route::post('/settings/slides/upload', 'SettingController@slidesUpload');
    Route::post('/settings/slides/delete/{id}', 'SettingController@deleteImageSlider');
    Route::post('/settings/slides/set', 'SettingController@setSlidesList');

    Route::post('/category/list', 'CategoryController@index');
    Route::post('/category/create', 'CategoryController@create');
    Route::post('/category/show/{id}', 'CategoryController@show');
    Route::post('/category/edit/{id}', 'CategoryController@edit');
    Route::post('/category/delete/{id}', 'CategoryController@delete');
    Route::post('/category/main-categories/{id?}', 'CategoryController@getMainCategories');

    Route::post('/product/list', 'ProductController@index');
    Route::post('/product/comments/list', 'ProductController@comments');
    Route::post('/product/comments/delete/{id}', 'ProductController@deleteComment');
    Route::post('/product/create', 'ProductController@create');
    Route::post('/product/show/{id}', 'ProductController@show');
    Route::post('/product/edit/{id}', 'ProductController@edit');
    Route::post('/product/delete/{id}', 'ProductController@delete');

    Route::post('/product/get-categories/{id?}', 'ProductController@getCategories');
    Route::post('/product/get-tags', 'ProductController@getTags');
    Route::post('/product/get-users', 'ProductController@getUsers');
    Route::post('/product/upload/{type}', 'ProductController@upload');

    Route::post('/assortment/list', 'AssortmentController@index');
    Route::post('/assortment/create', 'AssortmentController@create');
    Route::post('/assortment/show/{id}', 'AssortmentController@show');
    Route::post('/assortment/edit/{id}', 'AssortmentController@edit');
    Route::post('/assortment/delete/{id}', 'AssortmentController@delete');
    Route::post('/assortment/main-assortments/{id?}', 'AssortmentController@getMainAssortments');

    Route::post('/post/list', 'PostController@index');
    Route::post('/post/create', 'PostController@create');
    Route::post('/post/show/{id}', 'PostController@show');
    Route::post('/post/edit/{id}', 'PostController@edit');
    Route::post('/post/delete/{id}', 'PostController@delete');
    Route::post('/post/comments/list', 'PostController@comments');
    Route::post('/post/comments/delete/{id}', 'PostController@deleteComment');
    Route::post('/post/upload/image', 'PostController@upload');

    Route::post('/post/get-assortments/{id?}', 'PostController@getAssortments');
    Route::post('/post/get-tags', 'PostController@getTags');
    Route::post('/post/get-users', 'PostController@getUsers');

    Route::post('/tag/list', 'TagController@index');
    Route::post('/tag/create', 'TagController@create');
    Route::post('/tag/show/{id}', 'TagController@show');
    Route::post('/tag/edit/{id}', 'TagController@edit');
    Route::post('/tag/delete/{id}', 'TagController@delete');


    Route::post('/comments/show/{id}', 'ProductController@showComment');
    Route::post('/comments/edit/{id}', 'ProductController@editComment');

    Route::post('/user/list', 'UserController@index');
    Route::post('/user/create', 'UserController@create');
    Route::post('/user/show/{id}', 'UserController@show');
    Route::post('/user/edit/{id}', 'UserController@edit');
    Route::post('/user/delete/{id}', 'UserController@delete');
    Route::post('/user/upload-image', 'UserController@uploadImg');
    Route::post('/admin/list', 'UserController@indexAdmin');
    Route::post('/admin/show/{id}', 'UserController@showAdmin');
    Route::post('/admin/edit/{id}', 'UserController@editAdmin');

    Route::post('/orders/list', 'OrderController@lists');
    Route::post('/orders/list/failed', 'OrderController@failedLists');
    Route::post('/orders/show/{id}', 'OrderController@show');
    Route::post('/orders/delete/{id}', 'OrderController@delete');
    Route::post('/orders/save', 'OrderController@edit');
    Route::post('/orders/search-products', 'OrderController@searchProducts');


    Route::post('/transactions/list', 'FinanceController@getTransactionLists');
    Route::post('/transactions/create', 'FinanceController@createTransaction');
    Route::post('/checkout/list', 'FinanceController@getWithdrawalLists');
    Route::post('/checkout/show/{id}', 'FinanceController@getWithdrawal');
    Route::post('/checkout/save/{id}', 'FinanceController@updateWithdrawal');
    Route::post('/referral/list', 'FinanceController@getReferralList');

    // Tickets
    Route::post('/create-ticket', 'TicketController@createTicket');
    Route::post('/getAllUsers', 'TicketController@getAllUsers');
    Route::post('/reply-ticket', 'TicketController@replyTicket');
    Route::post('/get-ticket', 'TicketController@getTicket');
    Route::post('/del-ticket', 'TicketController@delTicket');
    Route::post('/get-tickets', 'TicketController@getTickets');

    Route::post('/notification/list', 'NotificationController@lists');
    Route::post('/notification/show/{id}', 'NotificationController@Show');
    Route::post('/notification/read/{id}', 'NotificationController@read');
    Route::post('/notification/unread-numbers', 'NotificationController@countUnread');


});

Route::group(['middleware' => ['web'], 'prefix' => '/management/'], function () {
    Route::get('/{any?}', function () {
        return view('dashboard')->with('pageTitle', env('APP_NAME'));
    })->name('dashboard.index')->where('any', '(.*)');;
});
