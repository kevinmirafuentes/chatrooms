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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::group(['prefix' => 'chat'], function() {
	Route::get('/', 'Chat\ChatController@index');
	Route::get('/users', 'Chat\UserController@index');
	Route::get('/chatrooms', 'Chat\ChatroomController@index');
	Route::post('/chatrooms', 'Chat\ChatroomController@store');
	Route::get('/chatrooms/{chatroom}/messages', 'Chat\MessageController@index');
	Route::post('/chatrooms/{chatroom}/messages', 'Chat\MessageController@store');
});