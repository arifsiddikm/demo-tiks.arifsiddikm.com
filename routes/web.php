<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\RedeemController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FilmController as AdminFilmController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/city/select', [HomeController::class, 'selectCity'])->name('city.select');

// Films
Route::get('/film/{slug}', [FilmController::class, 'show'])->name('films.show');
Route::get('/api/schedules', [FilmController::class, 'schedulesByCityDate'])->name('films.schedules');
Route::get('/api/seats/{scheduleId}', [FilmController::class, 'seats'])->name('films.seats');

// Redeem (public - for lobby kiosk)
Route::get('/redeem', [RedeemController::class, 'index'])->name('redeem.index');
Route::post('/redeem/check', [RedeemController::class, 'check'])->name('redeem.check');
Route::post('/redeem/confirm', [RedeemController::class, 'redeem'])->name('redeem.confirm');

// News
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Checkout
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/finish/{bookingCode}', [CheckoutController::class, 'finish'])->name('checkout.finish');

    // Payment
    Route::get('/payment/{bookingCode}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/snap-token', [PaymentController::class, 'requestSnapToken'])->name('payment.snap-token');
    Route::post('/payment/midtrans-success', [PaymentController::class, 'midtransSuccess'])->name('payment.midtrans-success');
    Route::get('/payment/finish', [PaymentController::class, 'finish'])->name('payment.finish');

    // My Bookings / Tickets
    Route::get('/tickets', [BookingController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{bookingCode}', [BookingController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{bookingCode}/pdf', [BookingController::class, 'downloadPdf'])->name('tickets.pdf');
});

/*
|--------------------------------------------------------------------------
| Midtrans / Riplabs Callback — no CSRF
|--------------------------------------------------------------------------
| Riplabs mengirim notifikasi ke: POST /payment/midtrans/notification
| Riplabs redirect finish ke:     GET  /payment/finish-redirect
*/
Route::post('/payment/midtrans/notification', [PaymentController::class, 'callback'])
    ->name('payment.callback')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/payment/finish-redirect', [PaymentController::class, 'finishRedirect'])
    ->name('payment.finish-redirect');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Films
    Route::resource('films', AdminFilmController::class)->except(['show']);
    Route::get('films/{film}/schedules', [AdminFilmController::class, 'schedules'])->name('films.schedules');
    Route::post('films/{film}/schedules', [AdminFilmController::class, 'storeSchedule'])->name('films.schedules.store');
    Route::delete('schedules/{schedule}', [AdminFilmController::class, 'destroySchedule'])->name('schedules.destroy');

    // Orders
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{booking}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{booking}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
    Route::post('orders/{booking}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Users
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::post('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // News
    Route::resource('news', AdminNewsController::class)->except(['show']);
});
