<?php

use App\Http\Controllers\KaryaSiswaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function () {
    // Public endpoints
    Route::get('/karya-siswa/published', [KaryaSiswaController::class, 'getPublished']);

    // Protected endpoints (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/karya-siswa', [KaryaSiswaController::class, 'store']);
    });
});
