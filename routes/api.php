<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IpInfoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/', function () {
    return 'API';
});

Route::post('/login', [AuthController::class, 'store'])->name('login.store');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', function (Request $request) { return $request->user(); });
    Route::get('ipinfo/current', [IpInfoController::class, 'geo']); // current IP
    Route::get('ipinfo/history', [IpInfoController::class, 'history']);
    Route::get('ipinfo/{ip}', [IpInfoController::class, 'geoByIp']); // specific IP
    Route::post('ipinfo', [IpInfoController::class, 'store']);
    Route::delete('/ipinfo', [IpInfoController::class, 'destroyMultiple']); 
});


