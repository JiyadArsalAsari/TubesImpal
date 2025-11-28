<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MahasiswaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('landing');
})->name('landing');

// Authentication Routes
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard Routes
Route::get('/mahasiswa/dashboard', [MahasiswaController::class, 'dashboard'])
    ->middleware('auth')
    ->name('mahasiswa.dashboard');

Route::get('/dosen/dashboard', function () {
    return 'Dosen Dashboard - Halaman ini hanya bisa diakses oleh dosen';
})->middleware('auth');