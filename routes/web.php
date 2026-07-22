<?php

use Illuminate\Support\Facades\Route;

// ===== User Area Controllers =====
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\OrganizationProfileController;

// ===== Admin Area Controllers =====
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\BuyerAuthController;
use App\Http\Controllers\Organizer\OrganizerAuthController;
use App\Http\Controllers\Admin\OrganizationApprovalController;
use App\Http\Controllers\Organizer\OrganizerDashboardController;
use App\Http\Controllers\Organizer\OrganizerEventController;
use App\Http\Controllers\Organizer\OrganizerCheckinController;
use App\Http\Controllers\Admin\AdminCheckinController;

/*
|--------------------------------------------------------------------------
| Satpam Pengarah Rute (Fallback Middleware Auth)
|--------------------------------------------------------------------------
*/
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');


/*
|--------------------------------------------------------------------------
| Rute Publik / User
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/tentang', fn() => view('about'))->name('about');
Route::get('/kontak', fn() => view('contact'))->name('contact');
Route::get('/profil', fn() => view('profil'))->name('profil');
Route::get('/katalog', fn() => view('katalog'))->name('katalog');
Route::get('/bantuan', fn() => view('bantuan'))->name('help');

Route::get('/event/{id}', [EventController::class, 'show'])->name('events.show');
Route::get('/my-ticket', [EventController::class, 'ticket'])->name('tickets.my');

Route::get('/checkout/{event}', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout/{event}', [CheckoutController::class, 'store'])->name('checkout.store');

Route::get('/payment/success/{order_id}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/payment/{order_id}', [CheckoutController::class, 'payment'])->name('checkout.payment');

// ----------------------------------------------------
// Webhook Midtrans (Payment Notification)
// ----------------------------------------------------
// PENTING: route ini dipanggil oleh SERVER Midtrans, bukan oleh
// browser user, jadi tidak boleh dilindungi CSRF (sudah dikecualikan
// di bootstrap/app.php). Fungsinya: kalau user membatalkan pembayaran
// atau transaksi expired dari sisi Midtrans, stok tiket dikembalikan
// secara real-time (tidak perlu menunggu cron tickets:release-expired).
Route::post('/midtrans/notification', [CheckoutController::class, 'notification'])
    ->name('midtrans.notification');

Route::get('/kategori', [HomeController::class, 'categories'])->name('categories.index');
Route::get('/kategori/{slug}', [HomeController::class, 'category'])->name('category.show');

// ----------------------------------------------------
// Login Instan via Google (SSO) - untuk Buyer
// ----------------------------------------------------
Route::get('/auth/google/redirect', [BuyerAuthController::class, 'redirectToGoogle'])
    ->name('buyer.google.redirect');

Route::get('/auth/google/callback', [BuyerAuthController::class, 'handleGoogleCallback'])
    ->name('buyer.google.callback');

Route::post('/logout', [BuyerAuthController::class, 'logout'])
    ->name('buyer.logout');

// ----------------------------------------------------
// Profil Publik Organizer (rekam jejak rating & review)
// ----------------------------------------------------
Route::get('/penyelenggara/{slug}', [OrganizationProfileController::class, 'show'])
    ->name('organizations.public.show');

// ----------------------------------------------------
// Rating & Review (wajib login sebagai buyer)
// ----------------------------------------------------
Route::post('/tickets/{transaction}/review', [ReviewController::class, 'store'])
    ->middleware('auth')
    ->name('reviews.store');


// ----------------------------------------------------
// Organizer (Kepanitiaan/HIMA)
// ----------------------------------------------------
Route::prefix('organizer')->name('organizer.')->group(function () {

    // --- Zona luar: belum login ---
    Route::get('register', [OrganizerAuthController::class, 'showRegister'])->name('register');
    Route::post('register', [OrganizerAuthController::class, 'register'])->name('register.store');
    Route::get('login', [OrganizerAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [OrganizerAuthController::class, 'login'])->name('login.post');

    // --- Perlu login, tapi TIDAK perlu status approved ---
    Route::middleware(['auth'])->group(function () {
        Route::get('pending', [OrganizerAuthController::class, 'pending'])->name('pending');
        Route::post('logout', [OrganizerAuthController::class, 'logout'])->name('logout');
    });

    // --- Zona dalam: WAJIB status approved (dicek OrganizerMiddleware) ---
    Route::middleware(['auth', 'organizer'])->group(function () {
        Route::get('dashboard', [OrganizerDashboardController::class, 'index'])->name('dashboard');
        Route::resource('events', OrganizerEventController::class)->except(['show']);

        // === Check-in Peserta ===
        // PENTING: sengaja ditaruh DI DALAM blok middleware ['auth','organizer']
        // ini (bukan di luar seperti sebelumnya) - supaya cuma organizer yang
        // sudah login & approved yang bisa akses halaman check-in ini.
        Route::get('checkin', [OrganizerCheckinController::class, 'index'])->name('checkin.index');
        Route::post('checkin', [OrganizerCheckinController::class, 'store'])->name('checkin.store');
    });
});


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

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('transactions', [\App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('transactions.index');

        Route::resource('events', AdminEventController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('partners', PartnerController::class);

        Route::get('organizations', [OrganizationApprovalController::class, 'index'])->name('organizations.index');
        Route::get('organizations/{organization}', [OrganizationApprovalController::class, 'show'])->name('organizations.show');
        Route::post('organizations/{organization}/approve', [OrganizationApprovalController::class, 'approve'])->name('organizations.approve');
        Route::post('organizations/{organization}/reject', [OrganizationApprovalController::class, 'reject'])->name('organizations.reject');

        // === Check-in Peserta (untuk event yang dibuat langsung oleh Admin) ===
        Route::get('checkin', [AdminCheckinController::class, 'index'])->name('checkin.index');
        Route::post('checkin', [AdminCheckinController::class, 'store'])->name('checkin.store');

    });

});