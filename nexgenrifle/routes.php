<?php

use Marty\NexGenRifle\Controllers\Api;
use Marty\NexGenRifle\Middleware\Authenticate;  // Fixed namespace capitalization

Route::group(['prefix' => 'api/v1', 'middleware' => [Authenticate::class]], function () {
    // Generic resource endpoints
    Route::get('resources/{resource}', [Api::class, 'index']);
    Route::post('resources/{resource}', [Api::class, 'store']);
    Route::get('resources/{resource}/{id}', [Api::class, 'show']);
    Route::put('resources/{resource}/{id}', [Api::class, 'update']);
    Route::delete('resources/{resource}/{id}', [Api::class, 'destroy']);

    // Process endpoint for flexible operations
    Route::post('process', [Api::class, 'process']);
    
    // Batch operations
    Route::post('batch', [Api::class, 'batch']);
    
    // Profile Types endpoints
    Route::get('profile-types', [Api::class, 'getProfileTypes']);
    Route::get('profile-types/{resource}', [Api::class, 'getProfileType']);
    
    // Legacy specific endpoints - these can be removed once clients migrate to the generic endpoints
    Route::get('build-lists', [Api::class, 'indexBuildLists']);
    Route::post('build-lists', [Api::class, 'storeBuildList']);
    Route::get('build-lists/{id}', [Api::class, 'showBuildList']);
    Route::put('build-lists/{id}', [Api::class, 'updateBuildList']);
    Route::delete('build-lists/{id}', [Api::class, 'destroyBuildList']);
});
