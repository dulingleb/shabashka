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

Route::middleware(['auth'])->namespace('User')->name('user.')->group(function (){
    Route::get('/settings', 'UserController@edit')->name('setting');
    Route::post('/settings/update', 'UserController@update')->name('setting.update');

    Route::resource('tasks', 'TaskController')->except(['show']);
    Route::post('task/setExecutor/{response}', 'TaskController@setExecutor')->name('task.setExecutor');
    Route::post('/tasks/upload-file', 'TaskController@uploadFile');
    Route::post('/tasks/remove-file', 'TaskController@removeFile');
    Route::post('/tasks/create', 'TaskController@store');
    Route::post('/tasks/{task}/add-review', 'TaskController@addReview')->name('task.addReview');

    Route::post('/tasks/response/add', 'ResponseController@store')->name('response.add');
    Route::post('/tasks/message/send', 'ResponseMessageController@store')->name('message.send');
});

Route::post('/tasks/list', 'User\TaskController@list');
Route::get('/tasks/{task}', 'User\TaskController@show')->name('tasks.show');

Route::get('/user/{user}', 'User\UserController@show')->name('user.show');

Route::get('/', function () {
    return view('welcome');
});


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

