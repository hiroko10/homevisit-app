<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitController;




Route::resource('clients', ClientController::class);

Route::get('/clients/{client}/visits/create', [VisitController::class, 'create']);
Route::post('/clients/{client}/visits', [VisitController::class, 'store']);

Route::get('/visits/{visit}/edit', [VisitController::class, 'edit']);
Route::put('/visits/{visit}', [VisitController::class, 'update']);
Route::delete('/visits/{visit}', [VisitController::class, 'destroy']);


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
