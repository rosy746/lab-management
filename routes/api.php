<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BotController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
/*
|--------------------------------------------------------------------------
| BOT API ROUTES
| Semua route di sini dilindungi BotAuthMiddleware
| Bot Python wajib kirim: Authorization: Bearer <BOT_TOKEN>
|--------------------------------------------------------------------------
*/
Route::prefix('bot')->middleware('bot.auth')->group(function () {

    // --- Identity ---
    Route::post('identify', [BotController::class, 'identify']);

    // --- Jadwal ---
    Route::get('jadwal/hari-ini',   [BotController::class, 'jadwalHariIni']);
    Route::get('jadwal/minggu-ini', [BotController::class, 'jadwalMingguIni']);

    // --- Booking ---
    Route::get('booking/pending',           [BotController::class, 'bookingPending']);
    Route::get('booking/hari-ini',          [BotController::class, 'bookingHariIni']);
    Route::post('booking/create',           [BotController::class, 'bookingCreate']);
    Route::post('booking/{id}/approve',     [BotController::class, 'bookingApprove']);
    Route::post('booking/{id}/reject',      [BotController::class, 'bookingReject']);

    // --- Labs & Slots ---
    Route::get('labs',                      [BotController::class, 'labs']);
    Route::get('labs/{id}/slots',           [BotController::class, 'labSlots']);

    // --- Sesi ---
    Route::get('sesi/aktif',               [BotController::class, 'sesiAktif']);
});






