<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group and "App\Http\Controllers\Api" namespace.
| and "api." route's alias name. Enjoy building your API!
|
*/

foreach (glob(__DIR__.'/api/*.php') as $routes) {
    include $routes;
}

// Families Routes.
Route::apiResource('families', 'FamilyController');
Route::get('/select/families', 'FamilyController@select')->name('families.select');

// SubCities Routes.
Route::apiResource('sub_cities', 'SubCityController');
Route::get('/select/sub_cities', 'SubCityController@select')->name('sub_cities.select');

// Neighborhoods Routes.
Route::apiResource('neighborhoods', 'NeighborhoodController');
Route::get('/select/neighborhoods', 'NeighborhoodController@select')->name('neighborhoods.select');

// Cities Routes.
Route::apiResource('cities', 'CityController');
Route::get('/select/cities', 'CityController@select')->name('cities.select');

// People Routes.
Route::apiResource('people', 'PersonController');
Route::get('/select/people', 'PersonController@select')->name('people.select');

// People Routes.
Route::apiResource('people', 'PersonController');
Route::get('/select/people', 'PersonController@select')->name('people.select');

// Complaints Routes.
Route::apiResource('complaints', 'ComplaintController');
Route::get('/select/complaints', 'ComplaintController@select')->name('complaints.select');

/*  The routes of generated crud will set here: Don't remove this line  */
