<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Category\Index;
use App\Http\Controllers\Category\Detail;
use App\Http\Controllers\Category\Store;
use App\Http\Controllers\Category\Update;
use App\Http\Controllers\Category\Destroy;

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

Route::get('{id}', Detail::class);
Route::put('{id}', Update::class);
Route::delete('{id}', Destroy::class);
Route::get('/', Index::class);
Route::post('/', Store::class);