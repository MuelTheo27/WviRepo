<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\FileController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\DeleteController;
use App\Http\Controllers\ProgressController;
Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

// Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
// });
    Route::prefix("/api")->group(function () {
            Route::post("/upload/xlsx", [FileController::class, "upload"])->name("upload.xlsx");
            Route::get("/results", [TableController::class, "getTableData"]);
            Route::post("/download", [DownloadController::class, "handle"])->name("download.xlsx");
            Route::post("/delete", [DeleteController::class, "handleDeletion"]);
            Route::get("/upload/progress", [ProgressController::class, "getUploadProgress"]);
            Route::get('/download/clear-progress', [ProgressController::class, "clearDownloadProgress"]);
            Route::get("/download/progress", [ProgressController::class, "getDownloadProgress"]);
        ;});
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
