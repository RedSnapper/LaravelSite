<?php

use Illuminate\Support\Facades\Route;
use RS\NView\Facades\NView;
use Illuminate\Http\Request;

use App\Http\Formlets\UserForm;
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
Route::resource('/role','RoleController');
Route::resource('/layout','LayoutController');
Route::resource('/segment','SegmentController');
Auth::routes();



