<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BreakController;
use App\Http\Controllers\CustomRegisterController;
use App\Http\Controllers\StampCorrectionRequestController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::middleware('auth')->group(function ()
{
    // メール認証画面
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    // メール認証とリダイレクト先
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/attendance');
    })->middleware('signed')
      ->name('verification.verify');

    // メール認証再送
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back();
    })->name('verification.send');

    Route::get('/attendance',[AttendanceController::class,'index'])->name('attendance');

    //勤怠一覧
    Route::get('/attendance/list/{ym?}',[AttendanceController::class,'list'])->name('attendance.list');

    //出勤
    Route::post('/attendance/start',[AttendanceController::class,'start'])->name('attendance.start');

    //退勤
    Route::post('/attendance/end',[AttendanceController::class,'end'])->name('attendance.end');

    // 休憩開始
    Route::post('/break/start',[BreakController::class,'start'])->name('break.start');

    //休憩終了
    Route::post('/break/end', [BreakController::class,'end'])->name('break.end');

    //ログアウト
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    //申請一覧
    Route::get('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'index'])->name('correction.list');

    //詳細編集
    Route::get('attendance/detail/{attendance}', [AttendanceController::class, 'show'])->name('attendance.detail');

    //保存処理
    Route::patch('attendance/detail/{attendance}', [StampCorrectionRequestController::class,'store'])->name('correction.store');
});

//ログイン
Route::post('/login', [AuthController::class, 'store'])->name('login');

//管理登録
Route::post('/register', [CustomRegisterController::class, 'store'])->name('register');