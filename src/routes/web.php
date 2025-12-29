<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\branch\PatrolController;
use App\Http\Controllers\branch\ScheduleController;
use App\Http\Controllers\branch\ScheduleShiftController;
use App\Http\Controllers\branch\TaskController;
use App\Http\Controllers\branch\UserController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});


Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('pages.dashboard.index');
    });
    // SUPERADMIN APP
    Route::middleware('superadmin.app')->group(function () {
        Route::controller(CompanyController::class)->group(function () {
            Route::get('company', 'index');
            Route::get('company/datatable', 'datatable')->name('company.datatable');
            Route::get('company/add', 'add');
            Route::post('company/store', 'store');
            Route::get('company/edit/{id}', 'edit');
        });
    });
    // SUPERADMIN COMPANY
    Route::middleware('superadmin.company')->group(function () {
        Route::controller(BranchController::class)->group(function () {
            Route::get('branch', 'index');
            Route::get('branch/datatable', 'datatable')->name('branch.datatable');
            Route::get('branch/add', 'add');
            Route::post('branch/store', 'store');
        });
    });
    Route::middleware('superadmin.branch')->group(function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('management-users', 'index');
            Route::get('management-users/add', 'add');
            Route::post('management-users/store', 'store');
            Route::get('management-users/datatable', 'datatable')->name('user.datatable');
        });
        Route::controller(ScheduleShiftController::class)->group(function () {
            Route::get('schedule-shift', 'index');
            Route::get('schedule-shift/add', 'add');
            Route::post('schedule-shift/store', 'store');
            Route::get('schedule-shift/datatable', 'datatable')->name('scheduleshift.datatable');
            Route::get('schedule-shift/generate', 'generateScheduleBalanced');
        });
        Route::controller(ScheduleController::class)->group(function () {
            Route::get('schedule-list', 'index');
            Route::get('schedule-list/datatable', 'datatable')->name('schedulelist.datatable');
        });
        Route::controller(TaskController::class)->group(function () {
            Route::get('tasks', 'index');
            // Route::get('schedule-list/datatable', 'datatable')->name('schedulelist.datatable');
        });
        Route::controller(PatrolController::class)->group(function () {
            Route::get('master-patroli', 'index');
            Route::get('master-patroli/add', 'add');
            Route::get('master-patroli/datatable', 'datatable')->name('master.patroli');
            Route::post('master-patroli/store', 'store');



            // Route::get('schedule-list/datatable', 'datatable')->name('schedulelist.datatable');
        });
    });







    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
