<?php

use App\Models\Admin;
use App\Models\Product;
use App\Models\ProductFile;
use App\Models\User;
use App\Services\Response\JsonResponse;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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
Route::get('/send-to-bank/{order_id}','Front\OrderController@redirectToBank')
    ->name('redirectToBank');

Route::any('/orders/verify','Front\OrderController@verify')
    ->name('verify');

Route::get('/', function () {
    return redirect('/management');
});
