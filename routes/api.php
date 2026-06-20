<?php

use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\MonitorApiController;
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

    Route::post('incidents/registrar/Comentario', [App\Http\Controllers\Api\IncidentApiController::class, 'registrarComentario']);

    Route::apiResource('incidents', App\Http\Controllers\Api\IncidentApiController::class)
        ->parameters(['incidents' => 'incident']);


    Route::prefix('analytics')->group(function () {
        Route::get('uptime-response', [AnalyticsController::class, 'getUptimeAndResponse']);
        Route::get('failure-patterns', [AnalyticsController::class, 'getFailurePatterns']);
        Route::get('live-status', [AnalyticsController::class, 'getLiveStatus']);
        Route::get('dashboard/summary', [AnalyticsController::class, 'getDashboardSummary']);
    });

    Route::middleware(['auth:sanctum', 'ability:monitor:access'])->group(function () {

        Route::get('/external/services', [MonitorApiController::class, 'getServicesToMonitor']);
        Route::post('/external/results', [MonitorApiController::class, 'storePingResult']);

    });


});

require __DIR__.'/auth.php';

Route::prefix('libres')->group(function () {

    require __DIR__.'/admin/Configuraciones/api_libres.php';

});



