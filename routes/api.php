<?php

use Illuminate\Http\Request;

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
Route::middleware(['guest:api'])->prefix('oauth')->namespace('User')->group(function (){
    Route::post('login', 'UserController@login');
    Route::post('register', 'UserController@register');
});

Route::middleware(['auth:api'])->prefix('user')->namespace('User')->name('user.')->group(function() {
    Route::get('{user}', 'UserController@show');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('tasks', 'TaskController@index');
