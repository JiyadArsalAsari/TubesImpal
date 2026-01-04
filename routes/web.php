<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\LearningDifficultyController;
use App\Http\Controllers\LearningRecommendationController;
use App\Http\Controllers\LearningRecommendationDetailController;
use App\Http\Controllers\LearningDevelopmentController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\DeadlineController;
use App\Http\Controllers\AdminController;

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

Route::get('/dosen/mahasiswa/{id}/development', [DosenController::class, 'viewStudentDevelopment'])
    ->middleware('auth')
    ->name('dosen.mahasiswa.development');

Route::post('/dosen/mahasiswa/{id}/feedback', [DosenController::class, 'storeStudentFeedback'])
    ->middleware('auth')
    ->name('dosen.student.feedback');

Route::delete('/dosen/mahasiswa-request/{id}/remove', [DosenController::class, 'removeMahasiswa'])
    ->middleware('auth')
    ->name('dosen.mahasiswa.remove');

Route::post('/dosen/request/{id}/resend', [DosenController::class, 'resendRequest'])
    ->middleware('auth')
    ->name('dosen.request.resend');

Route::delete('/dosen/request/{id}/cancel', [DosenController::class, 'cancelRequest'])
    ->middleware('auth')
    ->name('dosen.request.cancel');

Route::get(
    '/mahasiswa/learning-recommendation',
    [LearningRecommendationController::class, 'index']
)
    ->middleware('auth')
    ->name('mahasiswa.learning.recommendation');

Route::get(
    '/mahasiswa/learning-recommendation/{id}',
    [LearningRecommendationDetailController::class, 'show']
)
    ->middleware('auth')
    ->name('mahasiswa.learning.recommendation.detail');

// Notification Routes
Route::post('/mahasiswa/notifications/{id}/read', [MahasiswaController::class, 'markNotificationAsRead'])
    ->middleware('auth')
    ->name('mahasiswa.notifications.read');
Route::post('/mahasiswa/notifications/read-all', [MahasiswaController::class, 'markAllNotificationsAsRead'])
    ->middleware('auth')
    ->name('mahasiswa.notifications.readAll');
Route::delete('/mahasiswa/notifications/{id}', [MahasiswaController::class, 'deleteNotification'])
    ->middleware('auth')
    ->name('mahasiswa.notifications.delete');
Route::delete('/mahasiswa/notifications', [MahasiswaController::class, 'deleteAllNotifications'])
    ->middleware('auth')
    ->name('mahasiswa.notifications.deleteAll');

// Learning Development Route
Route::get('/mahasiswa/learning-development', [LearningDevelopmentController::class, 'index'])
    ->middleware('auth')
    ->name('mahasiswa.learning.development');

// Schedule Route
Route::get('/mahasiswa/schedule', [ScheduleController::class, 'index'])
    ->middleware('auth')
    ->name('mahasiswa.schedule');
Route::post('/mahasiswa/schedule', [ScheduleController::class, 'store'])
    ->middleware('auth')
    ->name('mahasiswa.schedule.store');
Route::delete('/mahasiswa/schedule/{id}', [ScheduleController::class, 'destroy'])
    ->middleware('auth')
    ->name('mahasiswa.schedule.destroy');

// Deadline Route
Route::get('/mahasiswa/deadline', [DeadlineController::class, 'index'])
    ->middleware('auth')
    ->name('mahasiswa.deadline');
Route::post('/mahasiswa/deadline', [DeadlineController::class, 'store'])
    ->middleware('auth')
    ->name('mahasiswa.deadline.store');
Route::delete('/mahasiswa/deadline/{id}', [DeadlineController::class, 'destroy'])
    ->middleware('auth')
    ->name('mahasiswa.deadline.destroy');

// Exercise Route
Route::get('/mahasiswa/exercise', [ExerciseController::class, 'mahasiswaIndex'])
    ->middleware('auth')
    ->name('mahasiswa.exercise');

Route::get('/mahasiswa/completed-exercises-json', [ExerciseController::class, 'getCompletedExercises'])
    ->middleware('auth')
    ->name('mahasiswa.completed.exercises.json');

// Dosen view student's exercises and grade assignments
Route::get('/dosen/mahasiswa/{id}/exercises', [DosenController::class, 'viewExercises'])
    ->middleware('auth')
    ->name('dosen.mahasiswa.exercises');
Route::post('/dosen/assignment/{submissionId}/grade', [DosenController::class, 'gradeAssignment'])
    ->middleware('auth')
    ->name('dosen.assignment.grade');
Route::post('/dosen/exercise/{exerciseId}/grade-manual', [DosenController::class, 'gradeManual'])
    ->middleware('auth')
    ->name('dosen.assignment.grade_manual');
Route::get('/dosen/assignment/{submissionId}/download', [DosenController::class, 'downloadSubmission'])
    ->middleware('auth')
    ->name('dosen.assignment.download');

Route::get('/mahasiswa/assignment/{id}', [ExerciseController::class, 'attemptAssignment'])
    ->middleware('auth')
    ->name('mahasiswa.assignment.attempt');

Route::get('/mahasiswa/assignment/{id}/download', [ExerciseController::class, 'downloadAssignment'])
    ->middleware('auth')
    ->name('mahasiswa.assignment.download');

Route::post('/mahasiswa/assignment/{id}/submit', [ExerciseController::class, 'submitAssignment'])
    ->middleware('auth')
    ->name('mahasiswa.assignment.submit');

Route::get('/mahasiswa/assignment/{id}/review', [ExerciseController::class, 'reviewAssignment'])
    ->middleware('auth')
    ->name('mahasiswa.assignment.review');

Route::get('/dosen/mahasiswa/{id}/exercise/create', [ExerciseController::class, 'createForMahasiswa'])
    ->middleware('auth')
    ->name('dosen.exercise.create');

Route::post('/dosen/mahasiswa/{id}/exercise', [ExerciseController::class, 'storeForMahasiswa'])
    ->middleware('auth')
    ->name('dosen.exercise.store');

Route::get('/dosen/exercise/{id}/edit', [ExerciseController::class, 'edit'])
    ->middleware('auth')
    ->name('dosen.exercise.edit');

Route::put('/dosen/exercise/{id}', [ExerciseController::class, 'update'])
    ->middleware('auth')
    ->name('dosen.exercise.update');

// Quiz Routes
Route::get('/dosen/mahasiswa/{id}/quiz/create', [QuizController::class, 'create'])
    ->middleware('auth')
    ->name('dosen.quiz.create');
Route::post('/dosen/mahasiswa/{id}/quiz', [QuizController::class, 'store'])
    ->middleware('auth')
    ->name('dosen.quiz.store');
Route::get('/mahasiswa/quiz/{exerciseId}/attempt', [QuizController::class, 'attempt'])
    ->middleware('auth')
    ->name('mahasiswa.quiz.attempt');
Route::post('/mahasiswa/quiz/{exerciseId}/submit', [QuizController::class, 'submitAttempt'])
    ->middleware('auth')
    ->name('mahasiswa.quiz.submit');
Route::get('/mahasiswa/quiz/{exerciseId}/review', [QuizController::class, 'review'])
    ->middleware('auth')
    ->name('mahasiswa.quiz.review');

// Debug route for testing
Route::get('/debug/quiz/{id}', function ($id) {
    try {
        $exercise = \App\Models\Exercise::with(['mahasiswa.user', 'dosen.user'])
            ->where('type', 'quiz')
            ->findOrFail($id);

        return response()->json([
            'exercise' => $exercise,
            'mahasiswa' => $exercise->mahasiswa,
            'dosen' => $exercise->dosen,
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->middleware('auth');

// Profile Settings Routes
Route::get('/profile', [AuthController::class, 'profile'])
    ->middleware('auth')
    ->name('profile.settings');
Route::post('/profile', [AuthController::class, 'updateProfile'])
    ->middleware('auth')
    ->name('profile.update');
Route::delete('/profile/photo', [AuthController::class, 'deleteProfilePhoto'])
    ->middleware('auth')
    ->name('profile.delete.photo');

// Admin Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/admin/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::get('/admin/users/{id}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/admin/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
});