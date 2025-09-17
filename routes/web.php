<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Staff\BookingController as StaffBookingController;
use App\Http\Controllers\Staff\FieldController as StaffFieldController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SportsFieldController as AdminFieldController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\FieldPublicController;

Route::get('/', fn() => view('welcome'))->name('home');

Route::get('/fields/{field}', [FieldPublicController::class, 'show'])->name('fields.show');
Route::get('/api/fields/{field}/events', [FieldPublicController::class, 'events'])->name('fields.events');
// ต้องล็อกอินก่อน
Route::middleware(['auth', 'verified'])->group(function () {


    Route::middleware('user')->group(function () {
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::delete('/bookings/{id}', [BookingController::class, 'destroy'])->name('bookings.destroy');
    });

    // STAFF routes
    Route::prefix('staff')->name('staff.')->middleware('staff')->group(function () {
        Route::get('/bookings', [StaffBookingController::class, 'index'])->name('bookings.index');
        Route::post('/bookings/{id}/approve', [StaffBookingController::class, 'approve'])->name('bookings.approve');
        Route::post('/bookings/{id}/reject', [StaffBookingController::class, 'reject'])->name('bookings.reject');
        Route::get('/fields/schedule', [StaffFieldController::class, 'schedule'])->name('fields.schedule');
        Route::get('/api/fields/{field}/events', [StaffFieldController::class, 'events'])->name('staff.fields.events');
    });

    // ADMIN routes
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('/users', AdminUserController::class);
        Route::resource('/fields', AdminFieldController::class);
        Route::resource('/announcements', AdminAnnouncementController::class);
    });
});

// require __DIR__.'/auth.php';
