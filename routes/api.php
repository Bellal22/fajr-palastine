<?php

use App\Http\Controllers\Dashboard\DataSyncController;
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


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// إضافة مسار لاستقبال البيانات من السيرفر الأول
Route::post('/sync-data', [DataSyncController::class, 'sync']);

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

// Suppliers Routes.
Route::apiResource('suppliers', 'SupplierController');
Route::get('/select/suppliers', 'SupplierController@select')->name('suppliers.select');

// AreaResponsibles Routes.
Route::apiResource('area_responsibles', 'AreaResponsibleController');
Route::get('/select/area_responsibles', 'AreaResponsibleController@select')->name('area_responsibles.select');

// Blocks Routes.
Route::apiResource('blocks', 'BlockController');
Route::get('/select/blocks', 'BlockController@select')->name('blocks.select');

// Items Routes.
Route::apiResource('items', 'ItemController');
Route::get('/select/items', 'ItemController@select')->name('items.select');

// Items Routes.
Route::apiResource('items', 'ItemController');
Route::get('/select/items', 'ItemController@select')->name('items.select');

// InboundShipments Routes.
Route::apiResource('inbound_shipments', 'InboundShipmentController');
Route::get('/select/inbound_shipments', 'InboundShipmentController@select')->name('inbound_shipments.select');

// Regions Routes.
Route::apiResource('regions', 'RegionController');
Route::get('/select/regions', 'RegionController@select')->name('regions.select');

// Locations Routes.
Route::apiResource('locations', 'LocationController');
Route::get('/select/locations', 'LocationController@select')->name('locations.select');

// Maps Routes.
Route::apiResource('maps', 'MapController');
Route::get('/select/maps', 'MapController@select')->name('maps.select');

// ReadyPackages Routes.
Route::apiResource('ready_packages', 'ReadyPackageController');
Route::get('/select/ready_packages', 'ReadyPackageController@select')->name('ready_packages.select');

// InternalPackages Routes.
Route::apiResource('internal_packages', 'InternalPackageController');
Route::get('/select/internal_packages', 'InternalPackageController@select')->name('internal_packages.select');

// PackageContents Routes.
Route::apiResource('package_contents', 'PackageContentController');
Route::get('/select/package_contents', 'PackageContentController@select')->name('package_contents.select');

// Projects Routes.
Route::apiResource('projects', 'ProjectController');
Route::get('/select/projects', 'ProjectController@select')->name('projects.select');

// SubWarehouses Routes.
Route::apiResource('sub_warehouses', 'SubWarehouseController');
Route::get('/select/sub_warehouses', 'SubWarehouseController@select')->name('sub_warehouses.select');

// OutboundShipments Routes.
Route::apiResource('outbound_shipments', 'OutboundShipmentController');
Route::get('/select/outbound_shipments', 'OutboundShipmentController@select')->name('outbound_shipments.select');

// OutboundShipmentItems Routes.
Route::apiResource('outbound_shipment_items', 'OutboundShipmentItemController');
Route::get('/select/outbound_shipment_items', 'OutboundShipmentItemController@select')->name('outbound_shipment_items.select');

// CouponTypes Routes.
Route::apiResource('coupon_types', 'CouponTypeController');
Route::get('/select/coupon_types', 'CouponTypeController@select')->name('coupon_types.select');

/*  The routes of generated crud will set here: Don't remove this line  */