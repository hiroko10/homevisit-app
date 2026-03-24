<?php

use App\Http\Controllers\ClientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('clients', [ClientController::class, 'apiIndex']); //一覧取得ルート

Route::get('/clients/{id}', [ClientController::class, 'apiShow']); //特定IDのデータを返すルート

Route::delete('/clients/{id}', [ClientController::class, 'apiDestroy']); //削除用ルート

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
