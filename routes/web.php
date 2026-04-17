<?php

use Illuminate\Support\Facades\Route;

// Import Controller User Area
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController; 

use App\Http\Controllers\Admin\DashboardController; 
use App\Http\Controllers\Admin\EventController as AdminEventController; 

/*
|--------------------------------------------------------------------------
| Rute Halaman Statis / Publik
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/tentang', function () {
    return view('about'); // Pastikan buat file about.blade.php
})->name('about');

Route::get('/kontak', function () {
    return view('contact');
})->name('contact');

Route::get('/profil', function () {
    return view('profil');
})->name('profil');

Route::get('/katalog', function () {
    return view('katalog');
})->name('katalog');

Route::get('/bantuan', function () {
    return view('bantuan');
})->name('help');

/*
|--------------------------------------------------------------------------
| Route Area User (Event & Transaksi)
|--------------------------------------------------------------------------
*/

Route::get('/event/{id}', [EventController::class, 'show'])->name('events.show');
Route::get('/my-ticket', [EventController::class, 'ticket']);
Route::get('/checkout', [EventController::class, 'checkout']);
/*
|--------------------------------------------------------------------------
| Route Area Admin (Grup dengan Prefix 'admin')
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    
    // URL: /admin/dashboard -> name: admin.dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // URL: /admin/events -> name: admin.events
    Route::get('/events', [AdminEventController::class, 'index'])->name('events');
    
    // URL: /admin/transactions -> name: admin.transactions
    Route::get('/transactions', function () {
        return view('admin.transactions');
    })->name('transactions');

});