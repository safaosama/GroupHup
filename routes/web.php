<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\SectionController;

Route::get('/', fn() => view('login'));
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboards
    Route::get('/student', [DashboardController::class, 'student']);
    Route::get('/student/courses', [DashboardController::class, 'studentCourses']);
    Route::get('/student/groups', [DashboardController::class, 'studentGroups']);
    Route::post('/student/groups/create', [GroupController::class, 'studentCreate']);
    Route::get('/student/notifications', [DashboardController::class, 'studentNotifications']);

    Route::get('/instructor', [DashboardController::class, 'instructor']);
    Route::get('/instructor/notifications', [DashboardController::class, 'instructorNotifications']);
    Route::get('/instructor/courses', [DashboardController::class, 'instructorCourses']);
    Route::get('/instructor/courses/{courseId}', [DashboardController::class, 'instructorCourseDetails']);
    Route::get('/instructor/courses/{course}/report', [DashboardController::class, 'generateReport']);
    Route::get('/instructor/sections/download-sample', [SectionController::class, 'downloadSample'])->name('instructor.sections.downloadSample');

    // Courses
    Route::post('/courses', [CourseController::class, 'store']);
    Route::delete('/courses/{id}', [CourseController::class, 'destroy']);

    // Sections
    Route::post('/courses/{courseId}/sections', [SectionController::class, 'store']);
    Route::post('/sections/{sectionId}/upload-students', [SectionController::class, 'uploadStudents']);
    Route::post('/sections/{sectionId}/add-student', [SectionController::class, 'addStudentById']);
    Route::post('/sections/{sectionId}/groups', [SectionController::class, 'storeGroup']);
    Route::post('/sections/{sectionId}/generate-groups', [SectionController::class, 'generateGroups']);

    // Groups - Instructor management
    Route::get('/groups/{groupId}', [GroupController::class, 'show']);
    Route::delete('/groups/{id}', [GroupController::class, 'destroy']);
    Route::post('/groups/{groupId}/add-member', [GroupController::class, 'addMember']);
    Route::delete('/groups/{groupId}/members/{userId}', [GroupController::class, 'removeMember']);

    // Groups - Student actions
    Route::post('/groups/student-create', [GroupController::class, 'studentCreate']);
    Route::post('/groups/{groupId}/join', [GroupController::class, 'join']);
    Route::post('/groups/{groupId}/leave', [GroupController::class, 'leave']);

    // Notifications
    Route::post('/notifications/{id}/mark-read', [DashboardController::class, 'markNotificationRead']);

});
