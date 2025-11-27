<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomRegisterController;
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


Route::get('/attendance', function () {
    return view('attendance');
})->name('attendance');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::post('/login', [AuthController::class, 'store'])->name('login');

Route::post('/register', [CustomRegisterController::class, 'store'])->name('register');



