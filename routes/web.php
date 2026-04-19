<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

// 1. トップページにアクセスしたら、ログインしていれば一覧へ
Route::get('/', function () {
    return redirect('/clients');
});

// 2. ログイン（auth）している時だけアクセスできるグループ
Route::middleware('auth')->group(function () {

    // --- 顧客一覧（Client） ---
    Route::get('/clients', [ClientController::class, 'Index'])->name('clients.index');
    Route::get('/api/clients', [ClientController::class, 'apiIndex'])->name('api.clients.index');
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::get('/clients/{id}', [ClientController::class, 'show'])->name('clients.show');
    // 必要に応じて、保存(store)や編集(edit)などのルートもここに追加

    // --- プロフィール（Breeze標準） ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Breezeが作ったダッシュボード
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';