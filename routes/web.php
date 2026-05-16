<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\InventoryPublicController;
use App\Http\Controllers\ScheduleAdminController;
use App\Http\Controllers\InventoryAdminController;
use App\Http\Controllers\RekapPublicController;
use App\Http\Controllers\AssignmentPublicController;
use App\Http\Controllers\AssignmentAdminController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\LabControlController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\LabClassController;

// ═══════════════════════════════════════════════════════════════
// PUBLIK
// ═══════════════════════════════════════════════════════════════

Route::get('/', [ScheduleController::class, 'index'])->name('home');

// Booking — max 10x per menit per IP
Route::post('/booking', [ScheduleController::class, 'storeBooking'])
    ->name('booking.store')
    ->middleware('throttle:10,1');

Route::post('/booking-minggu', [ScheduleController::class, 'storeSundayBooking'])
    ->name('sunday.booking.store')
    ->middleware('throttle:10,1');

// Kelas AJAX — max 30x per menit (dropdown bisa dipilih berkali-kali)
Route::get('/kelas', [ScheduleController::class, 'getClasses'])
    ->name('classes.list')
    ->middleware('throttle:30,1');

Route::get('/inventaris', [InventoryPublicController::class, 'index'])->name('inventory.public');
Route::get('/rekap', [RekapPublicController::class, 'index'])->name('rekap.public');

// ── Tugas publik ──
Route::get('/tugas', [AssignmentPublicController::class, 'index'])->name('assignment.public');
Route::get('/tugas/{assignment}', [AssignmentPublicController::class, 'show'])->name('assignment.show');

// Submit tugas — max 10x per menit
Route::post('/tugas/{assignment}/submit', [AssignmentPublicController::class, 'submit'])
    ->name('assignment.submit')
    ->middleware('throttle:10,1');

// ── Tugas admin (pakai token guru, tanpa login) ──
Route::get('/tugas-admin', [AssignmentAdminController::class, 'index'])->name('assignment.admin');

// Write operations — max 20x per menit
Route::middleware('throttle:20,1')->group(function () {
    Route::post('/tugas-admin', [AssignmentAdminController::class, 'store'])->name('assignment.store');
    Route::delete('/tugas-admin/{assignment}', [AssignmentAdminController::class, 'destroy'])->name('assignment.destroy');
    Route::post('/tugas-admin/submission/{submission}/grade', [AssignmentAdminController::class, 'gradeSubmission'])->name('assignment.grade');
});

// Download — max 30x per menit
Route::middleware('throttle:30,1')->group(function () {
    Route::get('/tugas-admin/submission/{submission}/download', [AssignmentAdminController::class, 'downloadSubmission'])->name('assignment.download');
    Route::get('/tugas/{assignment}/download-attachment', [AssignmentAdminController::class, 'downloadAttachment'])->name('assignment.download.attachment');
});

// ── Token guru — max 10x per menit (cegah brute force token) ──
Route::post('/guru/verify-token', [TeacherController::class, 'verifyToken'])
    ->name('teacher.verify')
    ->middleware('throttle:10,1');

// ── Fonnte webhook proxy ──
Route::any('/fonnte-webhook', function (\Illuminate\Http\Request $request) {
    $response = \Illuminate\Support\Facades\Http::timeout(10)
        ->post(env('BOT_URL', 'http://170.1.0.9:5000') . '/api/webhook/fonnte', $request->all());
    return response()->json($response->json());
})->middleware('throttle:60,1');

// ── Lab control (akses via link token) ──
Route::prefix('lab-control')->name('lab.')->group(function () {
    // Read — lebih longgar
    Route::get('/{token}', [LabControlController::class, 'control'])->name('control');
    Route::get('/{token}/status', [LabControlController::class, 'status'])
        ->name('status')
        ->middleware('throttle:60,1');

    // Write — lebih ketat (toggle internet & logout)
    Route::post('/{token}/toggle', [LabControlController::class, 'toggleInternet'])
        ->name('toggle')
        ->middleware('throttle:20,1');

    Route::post('/{token}/logout', [LabControlController::class, 'logout'])
        ->name('logout')
        ->middleware('throttle:10,1');
});

// ═══════════════════════════════════════════════════════════════
// GUEST
// ═══════════════════════════════════════════════════════════════

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:20,1');
});

// ═══════════════════════════════════════════════════════════════
// AUTH (perlu login)
// ═══════════════════════════════════════════════════════════════

Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard & views
    Route::get('/dashboard',   fn() => view('dashboard'))->name('dashboard');
    Route::get('/schedule',    fn() => view('dashboard'))->name('schedule.index');
    Route::get('/inventory',   fn() => view('dashboard'))->name('inventory.index');
    Route::get('/procurement', fn() => view('dashboard'))->name('procurement.index');

    // ── Booking admin ──
    Route::prefix('booking')->name('booking.')->group(function () {
        Route::get('/',                              [BookingController::class, 'index'])->name('index');
        Route::get('/{booking}',                     [BookingController::class, 'show'])->name('show');
        Route::patch('/{booking}/approve',           [BookingController::class, 'approve'])->name('approve');
        Route::post('/approve-group',                [BookingController::class, 'approveGroup'])->name('approve.group');
        Route::patch('/{id}/reject',                 [BookingController::class, 'reject'])->name('reject');
        Route::delete('/{booking}',                  [BookingController::class, 'destroy'])->name('destroy');
        Route::patch('/sunday/{id}/approve',         [BookingController::class, 'approveSunday'])->name('approve.sunday');
        Route::delete('/sunday/{id}',                [BookingController::class, 'destroySunday'])->name('destroy.sunday');
    });

    // ── Jadwal admin ──
    Route::prefix('jadwal-admin')->name('schedule.admin')->group(function () {
        Route::get('/',               [ScheduleAdminController::class, 'index'])->name('');
        Route::post('/',              [ScheduleAdminController::class, 'store'])->name('.store');
        Route::patch('/{schedule}',   [ScheduleAdminController::class, 'update'])->name('.update');
        Route::delete('/{schedule}',  [ScheduleAdminController::class, 'destroy'])->name('.destroy');
    });

    // ── Inventaris admin ──
    Route::prefix('inventaris-admin')->name('inventory.admin')->group(function () {
        Route::get('/',               [InventoryAdminController::class, 'index'])->name('');
        Route::post('/',              [InventoryAdminController::class, 'store'])->name('.store');
        Route::patch('/{inventory}',  [InventoryAdminController::class, 'update'])->name('.update');
        Route::delete('/{inventory}', [InventoryAdminController::class, 'destroy'])->name('.destroy');
    });

    // ── Guru ──
    Route::prefix('guru')->name('teacher.')->group(function () {
        Route::get('/',             [TeacherController::class, 'index'])->name('index');
        Route::post('/',            [TeacherController::class, 'store'])->name('store');
        Route::patch('/{teacher}',  [TeacherController::class, 'update'])->name('update');
        Route::delete('/{teacher}', [TeacherController::class, 'destroy'])->name('destroy');
    });

    // ── Sekolah & Kelas ──
    Route::prefix('sekolah')->name('organization.')->group(function () {
        Route::get('/',                  [OrganizationController::class, 'index'])->name('index');
        Route::post('/',                 [OrganizationController::class, 'store'])->name('store');
        Route::patch('/{organization}',  [OrganizationController::class, 'update'])->name('update');
        Route::delete('/{organization}', [OrganizationController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('kelas-admin')->name('class.')->group(function () {
        Route::get('/',           [LabClassController::class, 'index'])->name('index');
        Route::post('/',          [LabClassController::class, 'store'])->name('store');
        Route::patch('/{class}',  [LabClassController::class, 'update'])->name('update');
        Route::delete('/{class}', [LabClassController::class, 'destroy'])->name('destroy');
    });

    // ── Lab control admin ──
    Route::post('/lab-control-admin/generate', [LabControlController::class, 'generateToken'])
        ->name('lab.generate');
});