<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user()->responseUser();
});

Route::middleware('auth:sanctum')->group(function () {

    require __DIR__.'/admin/api.php';


    Route::apiResource('areas', App\Http\Controllers\Api\AreaApiController::class)
        ->parameters(['areas' => 'area']);

    Route::apiResource('notification_contacts', App\Http\Controllers\Api\NotificationContactApiController::class)
        ->parameters(['notification_contacts' => 'notificationcontact']);


    Route::apiResource('servers', App\Http\Controllers\Api\ServerApiController::class)
        ->parameters(['servers' => 'server']);

    Route::apiResource('services', App\Http\Controllers\Api\ServiceApiController::class)
        ->parameters(['services' => 'service']);

});

require __DIR__.'/auth.php';

Route::prefix('libres')->group(function () {

    require __DIR__.'/admin/Configuraciones/api_libres.php';

});

