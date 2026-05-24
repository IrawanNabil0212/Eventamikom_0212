<?php

use Illuminate\Support\Facades\Route;

// ===== User Area Controllers =====
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;

// ===== Admin Area Controllers =====
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PartnerController;

/*
|--------------------------------------------------------------------------
| Rute Publik / User
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Halaman Statis
Route::get('/tentang', fn() => view('about'))->name('about');
Route::get('/kontak', fn() => view('contact'))->name('contact');
Route::get('/profil', fn() => view('profil'))->name('profil');
Route::get('/katalog', fn() => view('katalog'))->name('katalog');
Route::get('/bantuan', fn() => view('bantuan'))->name('help');

// Event
Route::get('/event/{id}', [EventController::class, 'show'])->name('events.show');
Route::get('/my-ticket', [EventController::class, 'ticket'])->name('tickets.my');
Route::get('/checkout', [EventController::class, 'checkout'])->name('checkout');

// Kategori Publik
Route::get('/kategori', [HomeController::class, 'categories'])->name('categories.index');
Route::get('/kategori/{slug}', [HomeController::class, 'category'])->name('category.show');

/*
|--------------------------------------------------------------------------
| Rute Admin
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Transaksi (placeholder)
    Route::get('/transactions', fn() => view('admin.transactions'))->name('transactions');

    // CRUD Resources
    Route::resource('events', AdminEventController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('partners', PartnerController::class);

});