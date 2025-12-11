<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\LearningDifficultyController;
use App\Http\Controllers\LearningRecommendationController;
use App\Http\Controllers\DosenController;

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

// Dosen-Mahasiswa Request Routes
Route::get('/mahasiswa/dosen-requests', [MahasiswaController::class, 'getDosenRequests'])
    ->middleware('auth')
    ->name('mahasiswa.dosen.requests');
    
Route::post('/mahasiswa/dosen-requests/{id}/accept', [MahasiswaController::class, 'acceptDosenRequest'])
    ->middleware('auth')
    ->name('mahasiswa.dosen.requests.accept');
    
Route::post('/mahasiswa/dosen-requests/{id}/reject', [MahasiswaController::class, 'rejectDosenRequest'])
    ->middleware('auth')
    ->name('mahasiswa.dosen.requests.reject');

// Learning Difficulties Routes
Route::get('/mahasiswa/learning-difficulties', [LearningDifficultyController::class, 'index'])
    ->middleware('auth')
    ->name('mahasiswa.learning.difficulties');
    
Route::get('/mahasiswa/learning-difficulties/create', [LearningDifficultyController::class, 'create'])
    ->middleware('auth')
    ->name('mahasiswa.learning.difficulties.create');
    
Route::post('/mahasiswa/learning-difficulties', [LearningDifficultyController::class, 'store'])
    ->middleware('auth')
    ->name('mahasiswa.learning.difficulties.store');

Route::get('/dosen/dashboard', [DosenController::class, 'dashboard'])
    ->middleware('auth')
    ->name('dosen.dashboard');

Route::get('/dosen/search-mahasiswa', [DosenController::class, 'searchMahasiswa'])
    ->middleware('auth')
    ->name('dosen.search.mahasiswa');

Route::post('/dosen/request-add-mahasiswa', [DosenController::class, 'requestAddMahasiswa'])
    ->middleware('auth')
    ->name('dosen.request.add.mahasiswa');

Route::get('/dosen/mahasiswa/{id}/progress', [DosenController::class, 'viewLearningProgress'])
    ->middleware('auth')
    ->name('dosen.mahasiswa.progress');

Route::delete('/dosen/mahasiswa-request/{id}/remove', [DosenController::class, 'removeMahasiswa'])
    ->middleware('auth')
    ->name('dosen.mahasiswa.remove');

Route::get('/mahasiswa/learning-recommendation', 
    [LearningRecommendationController::class, 'index'])
    ->middleware('auth')
    ->name('mahasiswa.learning.recommendation');

Route::get('/mahasiswa/learning-recommendation/{id}', 
    [LearningRecommendationDetailController::class, 'show'])
    ->middleware('auth')
    ->name('mahasiswa.learning.recommendation.detail');

// Schedule Route
Route::get('/mahasiswa/schedule', [App\Http\Controllers\ScheduleController::class, 'index'])
    ->middleware('auth')
    ->name('mahasiswa.schedule');
Route::post('/mahasiswa/schedule', [App\Http\Controllers\ScheduleController::class, 'store'])
    ->middleware('auth')
    ->name('mahasiswa.schedule.store');
Route::delete('/mahasiswa/schedule/{id}', [App\Http\Controllers\ScheduleController::class, 'destroy'])
    ->middleware('auth')
    ->name('mahasiswa.schedule.destroy');

// Deadline Route
Route::get('/mahasiswa/deadline', [App\Http\Controllers\DeadlineController::class, 'index'])
    ->middleware('auth')
    ->name('mahasiswa.deadline');
Route::post('/mahasiswa/deadline', [App\Http\Controllers\DeadlineController::class, 'store'])
    ->middleware('auth')
    ->name('mahasiswa.deadline.store');
Route::delete('/mahasiswa/deadline/{id}', [App\Http\Controllers\DeadlineController::class, 'destroy'])
    ->middleware('auth')
    ->name('mahasiswa.deadline.destroy');