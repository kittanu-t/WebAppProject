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
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AnnouncementPublicController;
use App\Models\Announcement;


/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

// หน้า Home: แสดงประกาศล่าสุด 5 รายการ
Route::get('/', function () {
    $ann = Announcement::whereNotNull('published_at')
        ->orderByDesc('published_at')
        ->limit(5)
        ->get();

    // ถ้ามี resources/views/welcome.blade.php ก็ส่งไปที่หน้านี้
    return view('welcome', ['announcements' => $ann]);
})->name('home');

// หน้า Field List (ผู้ใช้ทั่วไปเข้าดูได้ ถ้าต้องการให้ล็อกอินค่อยย้ายเข้า group ด้านล่าง)
Route::get('/fields', [FieldPublicController::class, 'index'])->name('fields.index');

// หน้า Field Detail + Calendar (ผู้ใช้ทั่วไปเข้าดูได้ ถ้าต้องการให้ล็อกอินค่อยย้ายเข้า group ด้านล่าง)
Route::get('/fields/{field}', [FieldPublicController::class, 'show'])->name('fields.show');

// JSON events สำหรับ FullCalendar ฝั่งผู้ใช้ (public หรือจะย้ายไปหลัง auth ก็ได้)



/*
|--------------------------------------------------------------------------
| Authenticated routes (ต้องล็อกอิน + active + verified)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'verified'])->group(function () {

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::get('/notifications/feed', [NotificationController::class, 'feed'])->name('notifications.feed');
    Route::get('/announcements', [AnnouncementPublicController::class, 'index'])
    ->name('user.announcements.index');
    Route::get('/announcements/{announcement}', [AnnouncementPublicController::class, 'show'])
    ->name('user.announcements.show');

    // USER routes
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
        Route::get('/fields', [StaffFieldController::class, 'myFields'])->name('fields.index');
        Route::post('/fields/{field}/close', [StaffFieldController::class, 'close'])->name('fields.close');
        Route::post('/fields/{field}/open', [StaffFieldController::class, 'open'])->name('fields.open');
        Route::get('/fields/schedule', [StaffFieldController::class, 'schedule'])->name('fields.schedule');
        Route::get('/api/fields/{field}/events', [FieldPublicController::class, 'events'])->name('fields.events');

    });

    // ADMIN routes
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('/users', AdminUserController::class);
        Route::resource('/fields', AdminFieldController::class);
        Route::resource('/announcements', AdminAnnouncementController::class);
    });
});

