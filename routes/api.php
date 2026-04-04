<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\VisitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('clients', [ClientController::class, 'apiIndex']); //一覧取得ルート(取得)
Route::get('/clients/{id}', [ClientController::class, 'apiShow']); //特定IDのデータを返すルート(取得)

Route::post('/visits', [VisitController::class, 'store']); //履歴追加登録(登録)
Route::post('/clients', [ClientController::class, 'apiStore']); //新規登録(登録)

Route::put('/visits/{id}', [VisitController::class, 'update']); //訪問履歴の更新(更新)
Route::put('/clients/{id}', [ClientController::class, 'update']); //個人名・特徴メモの更新(更新)

Route::delete('/clients/{id}', [ClientController::class, 'apiDestroy']); //個人名削除用ルート(削除)
Route::delete('/visits/{visit}', [VisitController::class, 'Destroy']); //履歴削除用ルート(削除)


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
