<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\branch\AbsenController;
use App\Http\Controllers\branch\AnnouncementController;
use App\Http\Controllers\branch\LeaveController;
use App\Http\Controllers\branch\MenuHasRoleController;
use App\Http\Controllers\branch\PatrolController;
use App\Http\Controllers\branch\RoleController;
use App\Http\Controllers\branch\ScheduleController;
use App\Http\Controllers\branch\ScheduleShiftController;
use App\Http\Controllers\branch\TaskController;
use App\Http\Controllers\branch\UserBranchController;
use App\Http\Controllers\branch\UserController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Models\MasterPatroli;
use Illuminate\Support\Facades\Route;

// SUPERADMIN APP
require __DIR__.'/adminapp.php';


Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});


Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class , 'index']);


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
                Route::get('/edit/{id}', 'edit');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update');
                Route::get('/datatable', 'datatable')->name('user.datatable');
                Route::delete('delete/{id}',  'delete');
            });
        Route::prefix('schedule-shift')
            ->controller(ScheduleShiftController::class)->group(function () {
                Route::get('/', 'index');
                Route::get('/add', 'add');
                Route::post('/store', 'store');
                Route::get('/datatable', 'datatable')->name('scheduleshift.datatable');
                Route::get('/generate', 'generateScheduleBalanced');
                Route::get('/edit/{id}', 'edit');
                Route::post('/update/{id}', 'update');
                Route::delete('/delete/{id}', 'delete');
            });
        Route::prefix('schedule-list')
            ->controller(ScheduleController::class)->group(function () {
                Route::get('/', 'index');
                Route::get('/datatable', 'datatable')->name('schedulelist.datatable');
            });
        Route::prefix('tasks')
            ->controller(TaskController::class)->group(function () {
                Route::get('/', 'index');
                Route::get('/add', 'add');

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
        Route::prefix('core')
            ->group(
                function () {
                    Route::prefix('role')->controller(RoleController::class)
                        ->group(function () {
                            Route::get('/', 'index');
                            Route::get('/add', 'add');
                            Route::get('/edit/{id}', 'edit');
                            Route::post('/update/{id}', 'update');
                            Route::post('/store', 'store');
                            Route::get('/datatable', 'datatable')->name('role.datatable');
                            Route::delete('/delete/{id}', 'delete');
                        });
                    Route::prefix('menu-has-role')->controller(MenuHasRoleController::class)
                        ->group(function () {
                            Route::get('/', 'index');
                            Route::get('/add', 'add');
                            Route::get('/role', 'role')->name('role.ajax');
                            Route::get('/menu', 'menu')->name('menu.ajax');
                            Route::post('/store', 'store');
                            Route::get('/datatable', 'datatable')->name('role-menu.datatable');
                        });
                    Route::prefix('users')->controller(UserBranchController::class)
                        ->group(function () {
                            Route::get('/', 'index');
                            Route::get('/edit/{id}', 'edit');
                            Route::get('/add', 'add');
                            Route::post('/store', 'store');
                            Route::post('/update/{id}', 'update');
                            Route::get('/datatable', 'datatable')->name('user_branch.datatable');
                        });
                }
            );

        Route::prefix('leave')
            ->controller(LeaveController::class)->group(function () {
                Route::get('/', 'index');
                Route::get('/datatable', 'datatable')->name('leave.datatable');
            });
        Route::prefix('master-pengumuman')
            ->controller(AnnouncementController::class)->group(function () {
                Route::get('/', 'index');
                Route::get('/add', 'add');
                Route::get('/edit/{id}', 'edit');
                Route::post('/store', 'store');
                Route::post('/update/{id}', 'update');

                Route::delete('/delete/{id}', 'delete');
                Route::get('/datatable', 'datatable')->name('pengumuman.datatable');
            });
    });










    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
