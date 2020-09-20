<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\WordsController;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

//доступны без токена
Route::post('login',[UsersController::class, 'login']);
Route::post('register',[UsersController::class, 'register']);

// доступны с токеном
Route::group(['middleware' => 'auth:api'], function(){
  Route::get('words/{category}/{user_id}',[WordsController::class, 'getWords']);
  Route::post('words',[WordsController::class, 'addWord']);
  Route::patch('words/{id}',[WordsController::class, 'editWord']);
});
