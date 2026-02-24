<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\GradingController;
use App\Http\Controllers\Admin\LiveSessionController;
use App\Http\Controllers\Admin\LearningPathController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\QuestionBankController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserImportController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\GamificationController;
use App\Http\Controllers\LearningPathBrowseController;
use App\Http\Controllers\LessonViewController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizTakeController;
use App\Http\Controllers\StudentProgressController;
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

    // Student Progress
    Route::get('/progress', [StudentProgressController::class, 'index'])->name('progress.index');

    // Discussions
    Route::get('/courses/{course}/discussions', [DiscussionController::class, 'index'])->name('discussions.index');
    Route::post('/courses/{course}/discussions', [DiscussionController::class, 'store'])->name('discussions.store');
    Route::get('/courses/{course}/discussions/{discussion}', [DiscussionController::class, 'show'])->name('discussions.show');
    Route::post('/discussions/{discussion}/reply', [DiscussionController::class, 'reply'])->name('discussions.reply');
    Route::post('/discussions/{discussion}/pin', [DiscussionController::class, 'togglePin'])->name('discussions.pin')->middleware('role:super-admin|admin|instructor');
    Route::post('/discussions/{discussion}/lock', [DiscussionController::class, 'toggleLock'])->name('discussions.lock')->middleware('role:super-admin|admin|instructor');

    // Direct Messages
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'conversation'])->name('messages.conversation');
    Route::post('/messages/{user}', [MessageController::class, 'send'])->name('messages.send');

    // Announcements Feed
    Route::get('/announcements', [AnnouncementController::class, 'feed'])->name('announcements.feed');

    // Quiz Taking
    Route::post('/quiz/{quiz}/start', [QuizTakeController::class, 'start'])->name('quiz.start');
    Route::get('/quiz/attempt/{attempt}', [QuizTakeController::class, 'take'])->name('quiz.take');
    Route::post('/quiz/attempt/{attempt}/submit', [QuizTakeController::class, 'submit'])->name('quiz.submit');
    Route::get('/quiz/attempt/{attempt}/results', [QuizTakeController::class, 'results'])->name('quiz.results');

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

        // Quizzes (nested under courses)
        Route::resource('courses.quizzes', QuizController::class)->except(['show']);

        // Categories
        Route::resource('categories', CategoryController::class)->except(['show']);

        // Learning Paths
        Route::resource('learning-paths', LearningPathController::class)->except(['show']);

        // Question Banks
        Route::resource('question-banks', QuestionBankController::class)->except(['show']);

        // Grading
        Route::get('grading', [GradingController::class, 'index'])->name('grading.index');
        Route::post('grading/{attempt}', [GradingController::class, 'grade'])->name('grading.grade');

        // Analytics
        Route::get('analytics', [AnalyticsController::class, 'dashboard'])->name('analytics.dashboard');
        Route::get('analytics/course/{course}', [AnalyticsController::class, 'course'])->name('analytics.course');
        Route::get('analytics/gradebook/{course}', [AnalyticsController::class, 'gradeBook'])->name('analytics.gradebook');
        Route::get('analytics/export/{type}', [AnalyticsController::class, 'export'])->name('analytics.export');

        // Announcements
        Route::resource('announcements', AnnouncementController::class)->except(['show']);

        // Live Sessions (nested under courses)
        Route::resource('courses.live-sessions', LiveSessionController::class)->except(['show']);
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
