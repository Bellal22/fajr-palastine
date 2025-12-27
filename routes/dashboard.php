<?php

use App\Http\Controllers\Api\BlockController;
use App\Models\Person;
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

// Route::get('ec',function(){
//     try {
//         $person->load(['block', 'relatives']);

//         $data = [
//             'pid' => $person->id_num ?? '',
//             'fname' => $person->first_name ?? '',
//             'sname' => $person->father_name ?? '',
//             'tname' => $person->grandfather_name ?? '',
//             'lname' => $person->family_name ?? '',
//             'fcount' => $person->relatives_count ?? 0,
//             'mob_1' => $person->phone ?? '',
//             'mob_2' => '',
//             'block' => $person->block_id ?? '',
//             'note' => $person->notes ?? 'تم المزامنة تلقائياً',
//             'wife_id' => $person->getWifeId(),
//             'wife_name' => $person->getWifeName(),
//             'num_mail' => '',
//             'num_femail' => '',
//             'f_num_liss_3' => $person->getChildrenUnder3Count(),
//             'f_num_ill' => '',
//             'f_num_sn' => '',
//             'income' => '1',
//             'home_status' => $person->getHomeStatus(),
//             'date_of_birth' => $person->dob ? $person->dob->format('Y-m-d') : '',
//             'Original_governorate' => $person->original_governorate ?? '',
//             'marital_status' => $person->social_status ?? '',
//         ];

//         $response = Http::timeout(30)
//             ->withHeaders(['auth' => 'aaa@aaa@aaa@rrr'])
//             ->asMultipart()
//             ->post('https://aid.fajeryouth.org/public/API/convert/person/reg', $data);

//         if ($response->successful()) {
//             $person->update([
//                 'api_synced_at' => now(),
//                 'api_sync_status' => 'success'
//             ]);
//         } else {
//             $person->update([
//                 'api_sync_status' => 'failed',
//                 'api_sync_error' => $response->body()
//             ]);
//         }

//         return response()->json([
//             'person' => $person->first_name . ' ' . $person->family_name,
//             'status' => $response->status(),
//             'response' => $response->json() ?? $response->body(),
//         ]);
//     } catch (\Exception $e) {
//         return response()->json(['error' => $e->getMessage()], 500);
//     }
// });


Route::get('/sync-people-to-api', function () {
    try {
        $people = Person::with(['block', 'relatives'])->get();

        $results = [];
        $successCount = 0;
        $errorCount = 0;

        foreach ($people as $person) {
            try {
                $data = [
                    'pid' => $person->id_num ?? '',
                    'fname' => $person->first_name ?? '',
                    'sname' => $person->father_name ?? '',
                    'tname' => $person->grandfather_name ?? '',
                    'lname' => $person->family_name ?? '',
                    'fcount' => $person->relatives_count ?? 0,
                    'mob_1' => $person->phone ?? '',
                    'mob_2' => '',
                    'block' => $person->block_id ?? '',
                    'note' => $person->notes ?? 'تم المزامنة تلقائياً',
                    'wife_id' => $person->getWifeId(),
                    'wife_name' => $person->getWifeName(),
                    'num_mail' => '',
                    'num_femail' => '',
                    'f_num_liss_3' => $person->getChildrenUnder3Count(),
                    'f_num_ill' => '',
                    'f_num_sn' => '',
                    'income' => '1',
                    'home_status' => $person->getHomeStatus(),
                    'date_of_birth' => $person->dob ? $person->dob->format('Y-m-d') : '',
                    'Original_governorate' => $person->original_governorate ?? '',
                    'marital_status' => $person->social_status ?? '',
                ];

                $response = Http::timeout(30)
                    ->withHeaders(['auth' => 'aaa@aaa@aaa@rrr'])
                    ->asMultipart()
                    ->post('https://aid.fajeryouth.org/public/API/convert/person/reg', $data);

                if ($response->successful()) {
                    $person->update([
                        'api_synced_at' => now(),
                        'api_sync_status' => 'success'
                    ]);
                    $successCount++;
                } else {
                    $person->update([
                        'api_sync_status' => 'failed',
                        'api_sync_error' => $response->body()
                    ]);
                    $errorCount++;
                }
            } catch (\Exception $e) {
                $person->update([
                    'api_sync_status' => 'failed',
                    'api_sync_error' => $e->getMessage()
                ]);
                $errorCount++;
            }

            usleep(500000); // نصف ثانية بين كل طلب
        }

        return response()->json([
            'total' => $people->count(),
            'success' => $successCount,
            'errors' => $errorCount,
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// Route لمزامنة شخص واحد فقط
Route::get('/sync-person-to-api/{person}', function (Person $person) {
    try {
        $person->load(['block', 'relatives']);

        $data = [
            'pid' => $person->id_num ?? '',
            'fname' => $person->first_name ?? '',
            'sname' => $person->father_name ?? '',
            'tname' => $person->grandfather_name ?? '',
            'lname' => $person->family_name ?? '',
            'fcount' => $person->relatives_count ?? 0,
            'mob_1' => $person->phone ?? '',
            'mob_2' => '',
            'block' => $person->block_id ?? '',
            'note' => $person->notes ?? 'تم المزامنة تلقائياً',
            'wife_id' => $person->getWifeId(),
            'wife_name' => $person->getWifeName(),
            'num_mail' => '',
            'num_femail' => '',
            'f_num_liss_3' => $person->getChildrenUnder3Count(),
            'f_num_ill' => '',
            'f_num_sn' => '',
            'income' => '1',
            'home_status' => $person->getHomeStatus(),
            'date_of_birth' => $person->dob ? $person->dob->format('Y-m-d') : '',
            'Original_governorate' => $person->original_governorate ?? '',
            'marital_status' => $person->social_status ?? '',
        ];

        $response = Http::timeout(30)
            ->withHeaders(['auth' => 'aaa@aaa@aaa@rrr'])
            ->asMultipart()
            ->post('https://aid.fajeryouth.org/public/API/convert/person/reg', $data);

        if ($response->successful()) {
            $person->update([
                'api_synced_at' => now(),
                'api_sync_status' => 'success'
            ]);
        } else {
            $person->update([
                'api_sync_status' => 'failed',
                'api_sync_error' => $response->body()
            ]);
        }

        return response()->json([
            'person' => $person->first_name . ' ' . $person->family_name,
            'status' => $response->status(),
            'response' => $response->json() ?? $response->body(),
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

Route::get('/sync-status', function () {
    $stats = [
        'total' => Person::count(),
        'synced' => Person::where('api_sync_status', 'success')->count(),
        'failed' => Person::where('api_sync_status', 'failed')->count(),
        'pending' => Person::whereNull('api_sync_status')->count(),
    ];

    $recent_errors = Person::where('api_sync_status', 'failed')
        ->latest('updated_at')
        ->limit(10)
        ->select('id', 'first_name', 'family_name', 'api_sync_error')
        ->get();

    return response()->json([
        'statistics' => $stats,
        'recent_errors' => $recent_errors
    ]);
});

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
Route::get('people/export', 'PersonController@export')->name('people.export.selected'); // راوت الإكسبورت الجديد
Route::get('/people/export-view', 'PersonController@exportView')->name('people.export.view');
Route::get('/people/export-children', 'PersonController@exportChildren')->name('people.export.exportChildren');
Route::put('people/supervisor/assign/{person}', 'PersonController@assignToSupervisor')->name('people.assignToSupervisor');
Route::put('people/block/assign/{person}', 'PersonController@assignBlock')->name('people.assignBlock');
Route::put('people/block/assign-bulk', 'PersonController@assignBlocks')->name('people.assignBlocks');
Route::put('people/areaResponsible/delete/{person}', 'PersonController@deleteAreaResponsible')->name('people.areaResponsible.delete');
Route::put('people/areaResponsible/bulk-delete', 'PersonController@deleteAreaResponsibles')->name('people.areaResponsible.bulkDelete');
Route::post('people/assign-supervisor-block', 'PersonController@assignToUsers')->name('people.assignToUsers');
Route::get('ajax/blocks-by-responsible', 'PersonController@getBlocksByResponsible')->name('ajax.getBlocksByResponsible');
Route::get('people/view', 'PersonController@view')->name('people.view');
Route::get('people/search', 'PersonController@search')->name('people.search');
Route::post('people/clear', 'PersonController@clearSession')->name('people.clear');
Route::resource('people', 'PersonController');

Route::get('people/aid/api/{person}', 'PersonController@api')->name('people.aid.api');
Route::post('people/aid/bulk-sync', 'PersonController@bulkApiSync')->name('people.aid.bulk-sync');
Route::get('people/aid/sync-status', 'PersonController@syncStatus')->name('people.aid.sync-status');
Route::post('people/aid/retry-failed', 'PersonController@retryFailedSync')->name('people.aid.retry-failed');

// Complaints Routes.
Route::get('trashed/complaints', 'ComplaintController@trashed')->name('complaints.trashed');
Route::get('trashed/complaints/{trashed_complaint}', 'ComplaintController@showTrashed')->name('complaints.trashed.show');
Route::post('complaints/{trashed_complaint}/restore', 'ComplaintController@restore')->name('complaints.restore');
Route::delete('complaints/{trashed_complaint}/forceDelete', 'ComplaintController@forceDelete')->name('complaints.forceDelete');
Route::resource('complaints', 'ComplaintController');

// Suppliers Routes.
Route::get('trashed/suppliers', 'SupplierController@trashed')->name('suppliers.trashed');
Route::get('trashed/suppliers/{trashed_supplier}', 'SupplierController@showTrashed')->name('suppliers.trashed.show');
Route::post('suppliers/{trashed_supplier}/restore', 'SupplierController@restore')->name('suppliers.restore');
Route::delete('suppliers/{trashed_supplier}/forceDelete', 'SupplierController@forceDelete')->name('suppliers.forceDelete');
Route::resource('suppliers', 'SupplierController');

// AreaResponsibles Routes.
Route::get('trashed/area_responsibles', 'AreaResponsibleController@trashed')->name('area_responsibles.trashed');
Route::get('trashed/area_responsibles/{trashed_area_responsible}', 'AreaResponsibleController@showTrashed')->name('area_responsibles.trashed.show');
Route::post('area_responsibles/{trashed_area_responsible}/restore', 'AreaResponsibleController@restore')->name('area_responsibles.restore');
Route::delete('area_responsibles/{trashed_area_responsible}/forceDelete', 'AreaResponsibleController@forceDelete')->name('area_responsibles.forceDelete');
Route::resource('area_responsibles', 'AreaResponsibleController');
Route::post('area_responsibles/{areaResponsible}/refresh-count','AreaResponsibleController@refreshCount')->name('area-responsibles.refresh-count');

// Blocks Routes.
Route::get('trashed/blocks', 'BlockController@trashed')->name('blocks.trashed');
Route::get('trashed/blocks/{trashed_block}', 'BlockController@showTrashed')->name('blocks.trashed.show');
Route::post('blocks/{trashed_block}/restore', 'BlockController@restore')->name('blocks.restore');
Route::delete('blocks/{trashed_block}/forceDelete', 'BlockController@forceDelete')->name('blocks.forceDelete');
Route::get('blocks/by-area', [BlockController::class, 'getByAreaResponsible'])->name('blocks.byAreaResponsible');
Route::resource('blocks', 'BlockController');

// Items Routes.
Route::get('trashed/items', 'ItemController@trashed')->name('items.trashed');
Route::get('trashed/items/{trashed_item}', 'ItemController@showTrashed')->name('items.trashed.show');
Route::post('items/{trashed_item}/restore', 'ItemController@restore')->name('items.restore');
Route::delete('items/{trashed_item}/forceDelete', 'ItemController@forceDelete')->name('items.forceDelete');
Route::resource('items', 'ItemController');

// Items Routes.
Route::get('trashed/items', 'ItemController@trashed')->name('items.trashed');
Route::get('trashed/items/{trashed_item}', 'ItemController@showTrashed')->name('items.trashed.show');
Route::post('items/{trashed_item}/restore', 'ItemController@restore')->name('items.restore');
Route::delete('items/{trashed_item}/forceDelete', 'ItemController@forceDelete')->name('items.forceDelete');
Route::resource('items', 'ItemController');

// InboundShipments Routes.
Route::get('trashed/inbound_shipments', 'InboundShipmentController@trashed')->name('inbound_shipments.trashed');
Route::get('trashed/inbound_shipments/{trashed_inbound_shipment}', 'InboundShipmentController@showTrashed')->name('inbound_shipments.trashed.show');
Route::post('inbound_shipments/{trashed_inbound_shipment}/restore', 'InboundShipmentController@restore')->name('inbound_shipments.restore');
Route::delete('inbound_shipments/{trashed_inbound_shipment}/forceDelete', 'InboundShipmentController@forceDelete')->name('inbound_shipments.forceDelete');
Route::resource('inbound_shipments', 'InboundShipmentController');

// Regions Routes.
Route::get('trashed/regions', 'RegionController@trashed')->name('regions.trashed');
Route::get('trashed/regions/{trashed_region}', 'RegionController@showTrashed')->name('regions.trashed.show');
Route::post('regions/{trashed_region}/restore', 'RegionController@restore')->name('regions.restore');
Route::delete('regions/{trashed_region}/forceDelete', 'RegionController@forceDelete')->name('regions.forceDelete');
Route::get('regions/by-responsible/{responsible}', 'RegionController@showByResponsible')->name('regions.showByResponsible');
Route::resource('regions', 'RegionController');

// Locations Routes.
Route::get('trashed/locations', 'LocationController@trashed')->name('locations.trashed');
Route::get('trashed/locations/{trashed_location}', 'LocationController@showTrashed')->name('locations.trashed.show');
Route::post('locations/{trashed_location}/restore', 'LocationController@restore')->name('locations.restore');
Route::get('locations/area-responsible-by-region', 'LocationController@getAreaResponsibleByRegion')->name('locations.area-responsible-by-region');
Route::get('locations/blocks-by-area-responsible', 'LocationController@getBlocksByAreaResponsible')->name('locations.blocks-by-area-responsible');
Route::get('locations/region-boundaries', 'LocationController@getRegionWithBoundaries')->name('locations.region-boundaries');
Route::get('locations/map-data', 'LocationController@getMapData')->name('locations.map-data');
Route::get('locations/regions-data', 'LocationController@getRegionsData')->name('locations.regions-data');  // ✅ الجديد المفقود!
Route::resource('locations', 'LocationController');

// Maps Routes.
Route::get('trashed/maps', 'MapController@trashed')->name('maps.trashed');
Route::get('trashed/maps/{trashed_map}', 'MapController@showTrashed')->name('maps.trashed.show');
Route::post('maps/{trashed_map}/restore', 'MapController@restore')->name('maps.restore');
Route::delete('maps/{trashed_map}/forceDelete', 'MapController@forceDelete')->name('maps.forceDelete');
Route::resource('maps', 'MapController');

/*  The routes of generated crud will set here: Don't remove this line  */
