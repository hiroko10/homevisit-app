<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\VisitController;
use Illuminate\Support\Facades\Route;

// 1. トップページにアクセスしたら、自動的に顧客一覧画面（HTML）へ移動
Route::get('/', function () {
    return redirect('/clients');
});

// 2. ログイン（auth）している時だけアクセスできるグループ
Route::middleware('auth')->group(function () {

    // 画面表示（HTML）用のルート
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::get('/clients/{id}', [ClientController::class, 'show'])->name('clients.show');
    Route::get('/visits/create', [VisitController::class, 'create'])->name('visits.create');
    Route::get('/visits/{visit}/edit', [VisitController::class, 'edit'])->name('visits.edit');

    //音声入力(文字起こし用)
    Route::post('/clients/{client}/visits/speech-to-text', [VisitController::class, 'speechToText'])->name('visits.speech-to-text');


    // ===JavaScript（Axios）通信用のルートグループ===
    // prefix('api') をつけることで、この中のURLの頭には自動的に「/api」がつく
    Route::prefix('api')->group(function () {

        // --- 顧客関連（Clients） ---
        Route::get('/clients', [ClientController::class, 'apiIndex'])->name('api.clients.index'); // 一覧取得
        Route::post('/clients', [ClientController::class, 'apiStore']); // 新規登録
        Route::get('/clients/{id}', [ClientController::class, 'apiShow']); // 詳細取得
        Route::get('/clients/{id}/summarize', [ClientController::class, 'summarizeVisits']); //AI要約
        Route::put('/clients/{id}', [ClientController::class, 'update']); // 更新
        Route::delete('/clients/{id}', [ClientController::class, 'apiDestroy']); // 削除
        Route::put('/clients/{id}/favorite', [ClientController::class, 'toggleFavorite']); // 顧客お気に入り
        Route::get('/clients/{id}/activities/search', [ClientController::class, 'apiSearchActivities']); // 履歴検索

        // --- 訪問履歴関連（Visits） ---
        Route::get('/visits', [VisitController::class, 'index']); // 履歴一覧
        Route::post('/visits', [VisitController::class, 'store']); // 履歴登録
        Route::put('/visits/{id}', [VisitController::class, 'update']); // 履歴更新
        Route::delete('/visits/{id}', [VisitController::class, 'destroy']); // 履歴削除
        Route::patch('/visits/{visit}/favorite', [VisitController::class, 'toggleFavorite']); // 履歴お気に入り
    });


    // --- プロフィール関連（Breeze標準） ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';