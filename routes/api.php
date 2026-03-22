<?php

use App\Http\Controllers\ClientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('clients', [ClientController::class, 'apiIndex']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
