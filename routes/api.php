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


Route::get('/segments','SegmentController@branch');

Route::resource('/categories','CategoriesController',['except'=>['create','edit']]);

Route::put('/categories/{category}/moveTo','CategoriesController@moveTo');

Route::put('/categories/{category}/moveInto','CategoriesController@moveInto');
Route::put('/categories/{category}/moveBefore','CategoriesController@moveBefore');
Route::put('/categories/{category}/moveAfter','CategoriesController@moveAfter');