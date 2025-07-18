<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\SubscribeController;
use App\Http\Middleware\LogRequestResponse;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::prefix('v1')->group(function () {
//     Route::post('/subscribe', [SubscribeController::class, 'create'])
//         ->name('subscribe.create')->middleware(LogRequestResponse::class);
// });

Route::prefix('v1')
    ->middleware([LogRequestResponse::class])
    ->group(function () {
        Route::post('/subscriber/create', [SubscribeController::class, 'create'])->name('subscriber.create');
        Route::get('/subscriber/lists', [SubscribeController::class, 'getLists'])->name('subscriber.lists');
    });
