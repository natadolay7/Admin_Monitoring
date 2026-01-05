<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\branch\AbsenController;
use App\Http\Controllers\branch\PatrolController;
use App\Http\Controllers\branch\ScheduleController;
use App\Http\Controllers\branch\ScheduleShiftController;
use App\Http\Controllers\branch\TaskController;
use App\Http\Controllers\branch\UserController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CompanyController;
use App\Models\MasterPatroli;
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
        Route::prefix('management-users')
            ->controller(UserController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/add', 'add')->name('add');
                Route::post('/store', 'store')->name('store');
                Route::get('/datatable', 'datatable')->name('user.datatable');
            });
        Route::prefix('schedule-shift')
            ->controller(ScheduleShiftController::class)->group(function () {
                Route::get('/', 'index');
                Route::get('/add', 'add');
                Route::post('/store', 'store');
                Route::get('/datatable', 'datatable')->name('scheduleshift.datatable');
                Route::get('/generate', 'generateScheduleBalanced');
            });
        Route::prefix('schedule-list')
            ->controller(ScheduleController::class)->group(function () {
                Route::get('/', 'index');
                Route::get('/datatable', 'datatable')->name('schedulelist.datatable');
            });
        Route::prefix('tasks')
            ->controller(TaskController::class)->group(function () {
                Route::get('/', 'index');
                Route::get('/datatable', 'datatable')->name('task.datatable');
            });
        Route::prefix('master-patroli')
            ->controller(PatrolController::class)->group(function () {
                Route::get('/', 'index');
                Route::get('/add', 'add');
                Route::get('/datatable', 'datatable')->name('master.patroli');
                Route::post('/store', 'store');
                // Route::get('schedule-list/datatable', 'datatable')->name('schedulelist.datatable');
            });
        Route::prefix('report-absensi')
            ->controller(AbsenController::class)->group(function () {
                Route::get('/', 'index');
                Route::get('/datatable', 'datatable')->name('reportabsensi.datatable');
            });
         Route::prefix('report-patroli')
            ->controller(PatrolController::class)->group(function () {
                Route::get('/', 'report');
                Route::get('/datatable', 'reportDatatable')->name('reportpatroli.datatable');
            });
    });







    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
