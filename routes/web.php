<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AprController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TableController;
use App\Http\Services\Apr\AprService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SponsorController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/table', [SponsorController::class, 'index'])->name('table.index');


// Route::middleware('auth')->group(function () {
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
Route::prefix("/api")->group(function () {
    Route::post("/upload/xlsx", [FileController::class, "uploadXslx"])->name("upload.xlsx");
    Route::get("/results", [TableController::class, "getTableData"]);
    Route::get("/download", [DownloadController::class, "handle"])->name("download.xlsx");
    Route::get("/delete", [TableController::class, "deleteData"])
;});
// });


Route::get("/table", function () {
    return view("tabledata");
});
Route::get('/api/test',[AprService::class,'getPdfUrl'])->name('Api.Test');
require __DIR__.'/auth.php';

Route::get('admin/dashboard',[AdminController::class,'index'])->name('admin.home');


