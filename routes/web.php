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
Route::get('/home', 'HomeController@index')->name('home');

Route::resource('/user','UserController');
Route::resource('/category','CategoryController',['except'=>['create','edit','show']]);

//Teams
Route::resource('/team','TeamController', ['except' => 'show']);
Route::get('team/{category?}', 'TeamController@index')->name('team.index');
Route::get('team/create/{category?}', 'TeamController@create')->name('team.create');

//Activities
Route::resource('/activity','ActivityController', ['except' => 'show']);
Route::get('activity/{category?}', 'ActivityController@index')->name('activity.index');
Route::get('activity/create/{category?}', 'ActivityController@create')->name('activity.create');

//Layouts
Route::resource('/layout','LayoutController', ['except' => 'show']);
Route::get('layout/{category?}', 'LayoutController@index')->name('layout.index');
Route::get('layout/create/{category?}', 'LayoutController@create')->name('layout.create');

//Segments
Route::resource('/segment','SegmentController', ['except' => 'show']);
Route::get('segment/{category?}', 'SegmentController@index')->name('segment.index');
Route::get('segment/create/{category?}', 'SegmentController@create')->name('segment.create');

//Roles;
Route::resource('/role','RoleController', ['except' => 'show']);
Route::get('role/{category?}', 'RoleController@index')->name('role.index');
Route::get('role/create/{category?}', 'RoleController@create')->name('role.create');

//Tags;
Route::resource('/tag','TagController', ['except' => 'show']);
Route::get('tag/{category?}', 'TagController@index')->name('tag.index');
Route::get('tag/create/{category?}', 'TagController@create')->name('tag.create');

//Media
Route::get('/media/search','MediaController@search');
Route::resource('/media','MediaController',['except'=>'show']);
Route::get('media/{team}/{category?}', 'MediaController@index')->name('media.index');
Route::get('media/create/{team}/{category?}', 'MediaController@create')->name('media.create');
Route::get('/img/{media}','ImageController@show')->name('img.show');
Route::get('/img/{media}/thumbnail','ImageController@thumbnail')->name('img.thumbnail');


Auth::routes();



