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

Route::prefix('user')->namespace('User')->group(function() {

    Route::middleware(['auth:api'])->group(function (){
        Route::get('me', 'UserController@me');



    });

    Route::get('{user}', 'UserController@show');
});


Route::get('tasks', 'TaskController@index');
Route::get('task/{task}', 'TaskController@show');
Route::post('task/store', 'TaskController@store');
Route::delete('task/{task}', 'TaskController@destroy');



Route::get('categories', 'TaskController@categories');

