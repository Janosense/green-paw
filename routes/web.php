<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\LearningPathController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserImportController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\GamificationController;
use App\Http\Controllers\LearningPathBrowseController;
use App\Http\Controllers\LessonViewController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Google OAuth
    Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/courses', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/courses/{course:slug}', [CatalogController::class, 'show'])->name('catalog.show');

// Public learning paths
Route::get('/paths', [LearningPathBrowseController::class, 'index'])->name('paths.index');
Route::get('/paths/{path:slug}', [LearningPathBrowseController::class, 'show'])->name('paths.show');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'avatar'])->name('profile.avatar');

    /*
    |--------------------------------------------------------------------------
    | Learning Routes
    |--------------------------------------------------------------------------
    */

    Route::get('/my-courses', [EnrollmentController::class, 'myCourses'])->name('learn.my-courses');
    Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'enroll'])->name('learn.enroll');
    Route::delete('/courses/{course}/unenroll', [EnrollmentController::class, 'unenroll'])->name('learn.unenroll');
    Route::get('/courses/{course:slug}/lessons/{lesson:slug}', [LessonViewController::class, 'show'])->name('learn.lesson');
    Route::post('/lessons/{lesson}/complete', [LessonViewController::class, 'complete'])->name('learn.lesson.complete');

    // Gamification
    Route::get('/leaderboard', [GamificationController::class, 'leaderboard'])->name('gamification.leaderboard');
    Route::get('/badges', [GamificationController::class, 'badges'])->name('gamification.badges');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('admin')->name('admin.')->middleware('role:super-admin|admin|instructor')->group(function () {
        // Course Management
        Route::resource('courses', CourseController::class);
        Route::post('courses/{course}/publish', [CourseController::class, 'publish'])->name('courses.publish');
        Route::post('courses/{course}/unpublish', [CourseController::class, 'unpublish'])->name('courses.unpublish');
        Route::post('courses/{course}/duplicate', [CourseController::class, 'duplicate'])->name('courses.duplicate');
        Route::post('courses/{course}/new-version', [CourseController::class, 'newVersion'])->name('courses.new-version');

        // Lessons (nested under courses)
        Route::resource('courses.lessons', LessonController::class)->except(['index', 'show']);
        Route::post('courses/{course}/lessons/reorder', [LessonController::class, 'reorder'])->name('courses.lessons.reorder');

        // Categories
        Route::resource('categories', CategoryController::class)->except(['show']);

        // Learning Paths
        Route::resource('learning-paths', LearningPathController::class)->except(['show']);
    });

    Route::prefix('admin')->name('admin.')->middleware('role:super-admin|admin')->group(function () {
        // User Management
        Route::resource('users', UserController::class);

        // CSV Import
        Route::get('/users-import', [UserImportController::class, 'showForm'])->name('users.import');
        Route::post('/users-import', [UserImportController::class, 'import'])->name('users.import.process');
        Route::get('/users-import/template', [UserImportController::class, 'template'])->name('users.import.template');

        // Role Management
        Route::resource('roles', RoleController::class);
    });
});
