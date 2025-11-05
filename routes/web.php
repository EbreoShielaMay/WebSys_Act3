<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PortfolioController;
use Illuminate\Support\Facades\Route;


Route::get('/', [PortfolioController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/portfolio/edit', [PortfolioController::class, 'edit'])->name('portfolio.edit');
    Route::put('/portfolio/update', [PortfolioController::class, 'update'])->name('portfolio.update');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard page for authenticated users
    Route::view('/dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/auth.php';
