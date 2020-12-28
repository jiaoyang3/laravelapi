<?php


use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Config Routes
|--------------------------------------------------------------------------
|
| Here is where you can register routes for your application.
|
*/

Route::group(['prefix' => 'api', 'namespace' => 'Caps\LaravelApi\Controllers'], function () {
    Route::get('docs', 'ApiController@store');
});
