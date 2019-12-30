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
        Route::post('me', 'UserController@update');
    });

    Route::get('{user}', 'UserController@show');
});


Route::get('tasks', 'TaskController@index');
Route::get('task/{task}', 'TaskController@show');
Route::get('task/{task}/responses', 'User\ResponseController@index');

Route::middleware(['auth:api'])->prefix('task')->group(function() {
    Route::post('store', 'TaskController@store');
    Route::post('{task}', 'TaskController@update');
    Route::delete('{task}', 'TaskController@destroy');
    
    Route::post('{task}/response', 'User\ResponseController@store');
});




Route::get('categories', 'TaskController@categories');


