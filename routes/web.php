<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AprController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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

Route::get('/api/test',[AprController::class,'getApr'])->name('Api.Test');
require __DIR__.'/auth.php';

Route::get('admin/dashboard',[AdminController::class,'index'])->name('admin.home');
