<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard Routes (Placeholder)
Route::get('/mahasiswa/dashboard', function () {
    return 'Mahasiswa Dashboard - Halaman ini hanya bisa diakses oleh mahasiswa';
})->middleware('auth');

Route::get('/dosen/dashboard', function () {
    return 'Dosen Dashboard - Halaman ini hanya bisa diakses oleh dosen';
})->middleware('auth');
