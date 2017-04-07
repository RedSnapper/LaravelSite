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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('/category','CategoryController',['except'=>['create','edit']]);

Route::put('/category/{category}/moveInto','CategoryController@moveInto');
Route::put('/category/{category}/moveBefore','CategoryController@moveBefore');
Route::put('/category/{category}/moveAfter','CategoryController@moveAfter');