<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register dashboard routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "dashboard" middleware group and "App\Http\Controllers\Dashboard" namespace.
| and "dashboard." route's alias name. Enjoy building your dashboard!
|
*/

foreach (glob(__DIR__.'/dashboard/*.php') as $routes) {
    include $routes;
}

// Families Routes.
Route::get('trashed/families', 'FamilyController@trashed')->name('families.trashed');
Route::get('trashed/families/{trashed_family}', 'FamilyController@showTrashed')->name('families.trashed.show');
Route::post('families/{trashed_family}/restore', 'FamilyController@restore')->name('families.restore');
Route::delete('families/{trashed_family}/forceDelete', 'FamilyController@forceDelete')->name('families.forceDelete');
Route::resource('families', 'FamilyController');

// SubCities Routes.
Route::get('trashed/sub_cities', 'SubCityController@trashed')->name('sub_cities.trashed');
Route::get('trashed/sub_cities/{trashed_sub_city}', 'SubCityController@showTrashed')->name('sub_cities.trashed.show');
Route::post('sub_cities/{trashed_sub_city}/restore', 'SubCityController@restore')->name('sub_cities.restore');
Route::delete('sub_cities/{trashed_sub_city}/forceDelete', 'SubCityController@forceDelete')->name('sub_cities.forceDelete');
Route::resource('sub_cities', 'SubCityController');

// Neighborhoods Routes.
Route::get('trashed/neighborhoods', 'NeighborhoodController@trashed')->name('neighborhoods.trashed');
Route::get('trashed/neighborhoods/{trashed_neighborhood}', 'NeighborhoodController@showTrashed')->name('neighborhoods.trashed.show');
Route::post('neighborhoods/{trashed_neighborhood}/restore', 'NeighborhoodController@restore')->name('neighborhoods.restore');
Route::delete('neighborhoods/{trashed_neighborhood}/forceDelete', 'NeighborhoodController@forceDelete')->name('neighborhoods.forceDelete');
Route::resource('neighborhoods', 'NeighborhoodController');

// Cities Routes.
Route::get('trashed/cities', 'CityController@trashed')->name('cities.trashed');
Route::get('trashed/cities/{trashed_city}', 'CityController@showTrashed')->name('cities.trashed.show');
Route::post('cities/{trashed_city}/restore', 'CityController@restore')->name('cities.restore');
Route::delete('cities/{trashed_city}/forceDelete', 'CityController@forceDelete')->name('cities.forceDelete');
Route::resource('cities', 'CityController');

// People Routes.
Route::get('trashed/people', 'PersonController@trashed')->name('people.trashed');
Route::get('trashed/people/{trashed_person}', 'PersonController@showTrashed')->name('people.trashed.show');
Route::post('people/{trashed_person}/restore', 'PersonController@restore')->name('people.restore');
Route::delete('people/{trashed_person}/forceDelete', 'PersonController@forceDelete')->name('people.forceDelete');
Route::get('people/family/{person}', 'PersonController@listPersonFamily')->name('people.family.list');
Route::get('people/export', 'PersonController@export')->name('people.export'); // راوت الإكسبورت الجديد
Route::resource('people', 'PersonController');

// Complaints Routes.
Route::get('trashed/complaints', 'ComplaintController@trashed')->name('complaints.trashed');
Route::get('trashed/complaints/{trashed_complaint}', 'ComplaintController@showTrashed')->name('complaints.trashed.show');
Route::post('complaints/{trashed_complaint}/restore', 'ComplaintController@restore')->name('complaints.restore');
Route::delete('complaints/{trashed_complaint}/forceDelete', 'ComplaintController@forceDelete')->name('complaints.forceDelete');
Route::resource('complaints', 'ComplaintController');

/*  The routes of generated crud will set here: Don't remove this line  */