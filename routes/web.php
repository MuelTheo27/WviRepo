<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AprController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TableController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SponsorController;


Route::middleware(['auth', 'verified'])->group(function () {
    // Route for the dashboard page
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Redirect the home route to dashboard
    Route::get('/', function () {
        return redirect('dashboard');
    });
    
    // Other routes inside the middleware group for authenticated users
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::prefix("/api")->group(function () {
        Route::post("/upload/xlsx", [FileController::class, "uploadXslx"])->name("upload.xlsx");
        Route::get("/results", [TableController::class, "getTableData"]);
        Route::post("/download", [DownloadController::class, "handle"])->name("download.xlsx");
        Route::get("/delete", [TableController::class, "deleteData"]);
    });
    Route::get("/table", function () {
        return view("tabledata");
    });

    // Admin Dashboard Route
    Route::get('admin/dashboard',[AdminController::class,'index'])->name('admin.home');
});

require __DIR__.'/auth.php';
