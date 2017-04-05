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

//Activities
Route::get('/layout/activities','ActivityController@categories');
Route::resource('/activity','ActivityController', ['except' => 'update']);
Route::put('/activity/{activity}','ActivityController@update')->name('activity.update');

//Route::resource('/layout','LayoutController');
//Layouts
Route::get('/layout/categories','LayoutController@categories');
Route::resource('/layout','LayoutController', ['except' => 'update']);
Route::put('/layout/{layout}','LayoutController@update')->name('layout.update');

//Route::resource('/role','RoleController');
//Roles
Route::get('/role/categories','RoleController@categories');
Route::resource('/role','RoleController', ['except' => 'update']);
Route::put('/role/{role}','RoleController@update')->name('role.update');

//Segments
Route::get('/segment/categories','SegmentController@categories');
Route::resource('/segment','SegmentController', ['except' => 'update']);
Route::put('/segment/{segment}','SegmentController@update')->name('segment.update');

Auth::routes();



