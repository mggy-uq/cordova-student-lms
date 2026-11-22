<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\StudentAnnouncementsController;
use App\Http\Controllers\StudentAssignmentsController;
use App\Http\Controllers\StudentCoursesController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\StudentGradesController;
use App\Http\Controllers\StudentSettingsController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('login.store');

    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', StudentDashboardController::class)->name('dashboard');
    Route::get('/courses', StudentCoursesController::class)->name('courses.index');
    Route::get('/assignments', [StudentAssignmentsController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/{assignment}', [StudentAssignmentsController::class, 'show'])->name('assignments.show');
    Route::post('/assignments/{assignment}/submit', [StudentAssignmentsController::class, 'submit'])->name('assignments.submit');
    Route::get('/submissions/{submission}/download', [StudentAssignmentsController::class, 'download'])->name('submissions.download');
    Route::get('/grades', StudentGradesController::class)->name('grades.index');
    Route::get('/announcements', [StudentAnnouncementsController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/{announcement}', [StudentAnnouncementsController::class, 'show'])->name('announcements.show');
    Route::get('/settings', [StudentSettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('/settings/profile', [StudentSettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::put('/settings/password', [StudentSettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
