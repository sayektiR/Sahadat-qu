<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\AttendanceManagementController;
use App\Http\Controllers\Admin\AssessmentManagementController;
use App\Http\Controllers\Admin\GroupManagementController;
use App\Http\Controllers\Admin\PeriodManagementController;
use App\Http\Controllers\Admin\ReportManagementController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StudentManagementController;
use App\Http\Controllers\Admin\ScheduleManagementController;
use App\Http\Controllers\Admin\TeacherManagementController;
use App\Http\Controllers\Admin\SubjectManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Guardian\DashboardController as GuardianDashboardController;
use App\Http\Controllers\Leader\DashboardController as LeaderDashboardController;
use App\Http\Controllers\Leader\AccountController;
use App\Http\Controllers\Teacher\AssessmentController;
use App\Http\Controllers\Teacher\AttendanceController as TeacherAttendanceController;
use App\Http\Controllers\Teacher\AccountSettingController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\ReportsController as TeacherReportsController;
use App\Http\Controllers\Teacher\ScheduleController as TeacherScheduleController;
use App\Http\Controllers\Teacher\StudentController as TeacherStudentController;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GuardianImport;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//login//
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

//ketua lembaga//
Route::middleware('role:leader')->prefix('leader')->name('leader.')->group(function () {
    Route::get('/dashboard', LeaderDashboardController::class)->name('dashboard');
    Route::get('/branches', [App\Http\Controllers\Leader\BranchController::class, 'index'])->name('branches');
    Route::get('/branches/{branch}', [App\Http\Controllers\Leader\BranchController::class, 'show'])->name('branches.show');
    Route::post('/branches', [App\Http\Controllers\Leader\BranchController::class, 'store'])->name('branches.store');
    Route::put('/branches/{branch}', [App\Http\Controllers\Leader\BranchController::class, 'update'])->name('branches.update');
    Route::delete('/branches/{branch}', [App\Http\Controllers\Leader\BranchController::class, 'destroy'])->name('branches.destroy');
    Route::get('/admins', [App\Http\Controllers\Leader\AdminController::class, 'index'])->name('admins');
    Route::get('/admins/create', [App\Http\Controllers\Leader\AdminController::class, 'create'])->name('admins.create');
    Route::post('/admins', [App\Http\Controllers\Leader\AdminController::class, 'store'])->name('admins.store');
    Route::get('/admins/{admin}/edit', [App\Http\Controllers\Leader\AdminController::class, 'edit'])->name('admins.edit');
    Route::put('/admins/{admin}', [App\Http\Controllers\Leader\AdminController::class, 'update'])->name('admins.update');
    Route::delete('/admins/{admin}', [App\Http\Controllers\Leader\AdminController::class, 'destroy'])->name('admins.destroy');
    Route::get('/students', [App\Http\Controllers\Leader\StudentController::class, 'index'])->name('students');
    Route::get('/students/{student}', [App\Http\Controllers\Leader\StudentController::class, 'show'])->name('students.show');
    Route::get('/teachers', [App\Http\Controllers\Leader\TeacherController::class, 'index'])->name('teachers');
    Route::get('/teachers/{teacher}', [App\Http\Controllers\Leader\TeacherController::class, 'show'])->name('teachers.show');
    Route::get('/attendance', [App\Http\Controllers\Leader\AttendanceController::class, 'index'])->name('attendance');
    Route::get('/attendance/{attendance}', [App\Http\Controllers\Leader\AttendanceController::class, 'show'])->name('attendance.show');
    Route::get('/assessments', [App\Http\Controllers\Leader\AssessmentController::class, 'index'])->name('assessments');
    Route::get('/assessments/{assessment}', [App\Http\Controllers\Leader\AssessmentController::class, 'show'])->name('assessments.show');
    Route::get('/reports', [App\Http\Controllers\Leader\ReportController::class, 'index'])->name('reports');
    Route::get('/reports/{report}', [App\Http\Controllers\Leader\ReportController::class, 'show'])->name('reports.show');
    Route::get('/settings', [App\Http\Controllers\Leader\AccountController::class, 'index'])->name('settings');
    Route::put('/settings/account', [App\Http\Controllers\Leader\AccountController::class, 'updateAccount'])->name('settings.account.update');
    Route::put('/settings/password', [App\Http\Controllers\Leader\AccountController::class, 'updatePassword'])->name('settings.password.update');
});

//admin//
Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::get('/students', [StudentManagementController::class, 'index'])->name('students');
    Route::get('/students/create', [StudentManagementController::class, 'createStudent'])->name('students.create');
    Route::post('/students', [StudentManagementController::class, 'storeStudent'])->name('students.store');
    Route::get('/students/{student}/edit', [StudentManagementController::class, 'editStudent'])->name('students.edit');
    Route::put('/students/{student}', [StudentManagementController::class, 'updateStudent'])->name('students.update');
    Route::delete('/students/{student}', [StudentManagementController::class, 'destroyStudent'])->name('students.destroy');
    Route::get('/guardians', [StudentManagementController::class, 'guardians'])->name('guardians');
    Route::get('/guardians/create', [StudentManagementController::class, 'createGuardian'])->name('guardians.create');
    Route::post('/guardians', [StudentManagementController::class, 'storeGuardian'])->name('guardians.store');
    Route::get('/guardians/{guardian}/edit', [StudentManagementController::class, 'editGuardian'])->name('guardians.edit');
    Route::put('/guardians/{guardian}', [StudentManagementController::class, 'updateGuardian'])->name('guardians.update');
    Route::delete('/guardians/{guardian}', [StudentManagementController::class, 'destroyGuardian'])->name('guardians.destroy');
    Route::get('/guardians/search',[StudentManagementController::class, 'searchGuardians'])->name('guardians.search');
    Route::get('/teachers', [TeacherManagementController::class, 'index'])->name('teachers');
    Route::get('/teachers/create', [TeacherManagementController::class, 'create'])->name('teachers.create');
    Route::post('/teachers', [TeacherManagementController::class, 'store'])->name('teachers.store');
    Route::get('/teachers/{teacher}/edit', [TeacherManagementController::class, 'edit'])->name('teachers.edit');
    Route::put('/teachers/{teacher}', [TeacherManagementController::class, 'update'])->name('teachers.update');
    Route::delete('/teachers/{teacher}', [TeacherManagementController::class, 'destroy'])->name('teachers.destroy');
    Route::get('/groups', [GroupManagementController::class, 'index'])->name('groups');
    Route::post('/groups', [GroupManagementController::class, 'store'])->name('groups.store');
    Route::put('/groups/{group}', [GroupManagementController::class, 'update'])->name('groups.update');
    Route::delete('/groups/{group}', [GroupManagementController::class, 'destroy'])->name('groups.destroy');
    Route::get('/schedules', [ScheduleManagementController::class, 'index'])->name('schedules');
    Route::get('/schedules/create', [ScheduleManagementController::class, 'create'])->name('schedules.create');
    Route::post('/schedules', [ScheduleManagementController::class, 'store'])->name('schedules.store');
    Route::get('/schedules/{schedule}/edit', [ScheduleManagementController::class, 'edit'])->name('schedules.edit');
    Route::put('/schedules/{schedule}', [ScheduleManagementController::class, 'update'])->name('schedules.update');
    Route::delete('/schedules/{schedule}', [ScheduleManagementController::class, 'destroy'])->name('schedules.destroy');
    Route::get('/subjects', [SubjectManagementController::class, 'index'])->name('subjects.index');
    Route::post('/subjects', [SubjectManagementController::class, 'store'])->name('subjects.store');
    Route::put('/subjects/{subject}', [SubjectManagementController::class, 'update'])->name('subjects.update');
    Route::delete('/subjects/{subject}', [SubjectManagementController::class, 'destroy'])->name('subjects.destroy');
    Route::get('/attendance', [AttendanceManagementController::class, 'index'])->name('attendance');
    Route::get('/attendance/{attendance}/edit',[AttendanceManagementController::class, 'edit'])->name('attendance.edit');
    Route::put('/attendance/{attendance}', [AttendanceManagementController::class, 'update'])->name('attendance.update');
    Route::delete('/attendance/{attendance}', [AttendanceManagementController::class, 'destroy'])->name('attendance.destroy');
    Route::get('/assessments', [AssessmentManagementController::class, 'index'])->name('assessments');
    Route::get('/reports', [ReportManagementController::class, 'index'])->name('reports');
    Route::get('/reports/{report}', [ReportManagementController::class, 'show'])->name('reports.show');
    Route::get('/periods', [PeriodManagementController::class, 'index'])->name('periods');
    Route::post('/periods', [PeriodManagementController::class, 'store'])->name('periods.store');
    Route::put('/periods/{period}', [PeriodManagementController::class, 'update'])->name('periods.update');
    Route::delete('/periods/{period}', [PeriodManagementController::class, 'destroy'])->name('periods.destroy');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::put('/settings/branch', [SettingController::class, 'updateBranch'])->name('settings.branch.update');
    Route::put('/settings/account', [SettingController::class, 'updateAccount'])->name('settings.account.update');
    Route::get('/settings/assessments/assessment-template', [SettingController::class, 'assessmentTemplates'])->name('settings.assessments.assessment-template');
    Route::post('/settings/assessments/assessment-template', [AssessmentManagementController::class, 'store'])->name('settings.assessments.assessment-template.store');
    Route::put('/settings/assessments/assessment-template/{assessmentTemplate}/update', [AssessmentManagementController::class, 'update'])->name('settings.assessments.assessment-template.update');
    Route::delete('/settings/assessments/assessment-template/{assessmentTemplate}/destroy', [AssessmentManagementController::class, 'destroy'])->name('settings.assessments.assessment-template.destroy');
    Route::get('/settings/assessments/assessment-template/{assessmentTemplate}/attributes', [AssessmentManagementController::class, 'attributes'])->name('settings.assessments.assessment-template.attributes');
    Route::get('/settings/assessments/assessment-template/{assessmentTemplate}/aspects', [AssessmentManagementController::class, 'aspects'])->name('settings.assessments.assessment-template.aspects');
    Route::post('/settings/assessments/assessment-template/{assessmentTemplate}/attributes', [AssessmentManagementController::class, 'storeAttribute'])->name('settings.assessments.assessment-template.attributes.store');
    Route::put('/settings/assessments/assessment-template/{assessmentTemplate}/attributes/{attribute}', [AssessmentManagementController::class, 'updateAttribute'])->name('settings.assessments.assessment-template.attributes.update');
    Route::delete('/settings/assessments/assessment-template/{assessmentTemplate}/attributes/{attribute}', [AssessmentManagementController::class, 'destroyAttribute'])->name('settings.assessments.assessment-template.attributes.destroy');
    Route::post('/settings/assessments/assessment-template/{assessmentTemplate}/aspects', [AssessmentManagementController::class, 'storeAspect'])->name('settings.assessments.assessment-template.aspects.store');
    Route::put('/settings/assessments/assessment-template/{assessmentTemplate}/aspects/{aspect}', [AssessmentManagementController::class, 'updateAspect'])->name('settings.assessments.assessment-template.aspects.update');
    Route::delete('/settings/assessments/assessment-template/{assessmentTemplate}/aspects/{aspect}', [AssessmentManagementController::class, 'destroyAspect'])->name('settings.assessments.assessment-template.aspects.destroy');
    Route::get('/guardians/import/template', [StudentManagementController::class, 'guardianTemplate'])->name('guardians.import.template');
    Route::post('/guardians/import', [StudentManagementController::class, 'importGuardians'])->name('guardians.import');
    Route::get('/students/import/template', [StudentManagementController::class, 'studentTemplate'])->name('students.import.template');
    Route::post('/students/import', [StudentManagementController::class, 'importStudents'])->name('students.import');
});


//guru//
Route::middleware('role:teacher')->prefix('teachers')->name('teachers.')->group(function () {
    Route::get('/dashboard', TeacherDashboardController::class)->name('dashboard');
    Route::get('/students', [TeacherStudentController::class, 'index'])->name('students');
    Route::get('/schedules', [TeacherScheduleController::class, 'index'])->name('schedules');
    Route::get('/attendance', [TeacherAttendanceController::class, 'index'])->name('attendance');
    Route::post('/attendance', [TeacherAttendanceController::class, 'store'])->name('attendance.store');
    Route::delete('/attendance/{attendance}', [TeacherAttendanceController::class, 'destroy'])->name('attendance.destroy');
    Route::get('/assessments', [AssessmentController::class, 'index'])->name('assessments.index');
    Route::post('/assessments', [AssessmentController::class, 'store'])->name('assessments.store');
    Route::delete('/assessments/{assessment}', [AssessmentController::class, 'destroy'])->name('assessments.destroy');
    Route::get('/assessments/{assessment}', [AssessmentController::class, 'show'])->name('assessments.show');
    Route::get('/reports', [TeacherReportsController::class, 'index'])->name('reports');
    Route::get('/reports/{report}', [TeacherReportsController::class, 'show'])->name('reports.show');
    Route::get('/groups/{group}/students', [AssessmentController::class, 'students'])->name('groups.students');
    Route::get('/settings', [AccountSettingController::class, 'index'])->name('settings');
    Route::put('/settings/profile', [AccountSettingController::class, 'updateAccount'])->name('settings.profile.update');
    Route::put('/settings/password', [AccountSettingController::class, 'updatePassword'])->name('settings.password.update');
});

//wali murid//
Route::middleware('role:guardian')->prefix('guardians')->name('guardians.')->group(function () {
    Route::get('/dashboard', GuardianDashboardController::class)->name('dashboard');
    Route::get('/students', [App\Http\Controllers\Guardian\ProfileController::class, 'index'])->name('students');
    Route::get('/schedules', [App\Http\Controllers\Guardian\ScheduleController::class, 'index'])->name('schedules');
    Route::get('/attendance', [App\Http\Controllers\Guardian\AttendanceController::class, 'index'])->name('attendance');
    Route::get('/lesson-scores', [App\Http\Controllers\Guardian\LessonScoreController::class, 'index'])->name('lesson-scores');
    Route::get('/reports', [App\Http\Controllers\Guardian\ReportController::class, 'index'])->name('reports');
    Route::get('/reports/{report}', [App\Http\Controllers\Guardian\ReportController::class, 'show'])->name('reports.show');
    Route::get('/settings', [App\Http\Controllers\Guardian\SettingAccountController::class, 'index'])->name('settings');
    Route::put('/settings/profile', [App\Http\Controllers\Guardian\SettingAccountController::class, 'updateAccount'])->name('settings.profile.update');
    Route::put('/settings/password', [App\Http\Controllers\Guardian\SettingAccountController::class, 'updatePassword'])->name('settings.password.update');
});
