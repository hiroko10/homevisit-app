<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\VisitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('clients', [ClientController::class, 'apiIndex']); //一覧取得ルート
Route::get('/clients/{id}', [ClientController::class, 'apiShow']); //特定IDのデータを返すルート

Route::post('/visits', [VisitController::class, 'store']);

Route::delete('/clients/{id}', [ClientController::class, 'apiDestroy']); //削除用ルート(個人)
Route::delete('/visits/{visit}', [VisitController::class, 'Destroy']); //削除用ルート(履歴)


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
