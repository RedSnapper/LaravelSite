<?php

use Illuminate\Support\Facades\Route;
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

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');

Route::resource('/user','UserController');

Route::get('/layout/cats','LayoutController@cats');
Route::resource('/layout','LayoutController');

Route::get('/role/cats','RoleController@cats');
Route::resource('/role','RoleController');

Route::get('/segment/cats','SegmentController@cats');
Route::resource('/segment','SegmentController', ['except' => 'update']);
Route::patch('/segment/{segment}','SegmentController@patch')->name('segment.patch');
Route::put('/segment/{segment}','SegmentController@update')->name('segment.update');


Auth::routes();



