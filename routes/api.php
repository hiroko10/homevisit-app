<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    // --- ログインユーザー情報取得 ---
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

});