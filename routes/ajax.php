<?php

use Illuminate\Support\Facades\Route;

Route::get('/segments','SegmentController@branch');

Route::resource('/categories','CategoriesController');