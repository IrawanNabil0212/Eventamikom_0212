<?php

use Illuminate\Support\Facades\Route;

// ===== User Area Controllers =====
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CheckoutController;

// ===== Admin Area Controllers =====
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\TransactionController;

/*
|--------------------------------------------------------------------------
| Satpam Pengarah Rute (Fallback Middleware Auth)
|--------------------------------------------------------------------------
| Baris ini bertugas menangkap user tidak dikenal yang mencoba masuk ke
| dashboard, lalu mengarahkannya ke halaman login admin secara halus.
*/
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');


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

// Event Publik
Route::get('/event/{id}', [EventController::class, 'show'])->name('events.show');
Route::get('/my-ticket', [EventController::class, 'ticket'])->name('tickets.my');

// ===== Rute Checkout (Diperbarui) =====
Route::get('/checkout/{event}', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout/{event}', [CheckoutController::class, 'store'])->name('checkout.store');

//Payment
Route::get('/payment/success/{order_id}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/payment/{order_id}', [CheckoutController::class, 'payment'])->name('checkout.payment');
// Kategori Publik
Route::get('/kategori', [HomeController::class, 'categories'])->name('categories.index');
Route::get('/kategori/{slug}', [HomeController::class, 'category'])->name('category.show');


/*
|--------------------------------------------------------------------------
| Rute Admin (Struktur Bertumpuk & Dilindungi Middleware)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    // ----------------------------------------------------
    // ZONA LUAR: Bebas akses (Belum login bisa buka)
    // ----------------------------------------------------
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // ----------------------------------------------------
    // ZONA DALAM: Terkunci Gembok Middleware
    // ----------------------------------------------------
    Route::middleware(['auth', 'admin'])->group(function () {
        
        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // // Transaksi 
        // Route::get('transactions', [TransactionController::class, 'index'])->name('transactions');
        //transactions
        Route::get('transactions', [\App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('transactions.index');

        // CRUD Resources Admin
        Route::resource('events', AdminEventController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('partners', PartnerController::class);

        
        
    });

});