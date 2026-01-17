<?php

use App\Http\Controllers\adminapp\AbsenController;
use App\Http\Controllers\adminapp\BranchController;
use App\Http\Controllers\adminapp\UserTadController;
use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::prefix('v1')->middleware('superadmin.app')->group(function () {
        Route::controller(CompanyController::class)->group(function () {
            Route::get('company', 'index');
            Route::get('company/datatable', 'datatable')->name('company.datatable');
            Route::get('company/add', 'add');
            Route::post('company/store', 'store');
            Route::post('company/update/{id}', 'update');
            Route::get('company/edit/{id}', 'edit');
            Route::get('company/delete/{id}',  'delete');
        });
        Route::get('/get-company', [UserTadController::class, 'getCompany']);
        Route::get('/get-branch/{company_id}', [UserTadController::class, 'getBranch']);
        Route::prefix('management-users')
            ->controller(UserTadController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::get('/add', 'add')->name('add');
                Route::get('/edit/{id}', 'edit');
                Route::post('/store', 'store')->name('store');
                Route::post('/update/{id}', 'update');
                Route::get('/datatable', 'datatable')->name('v1.user.datatable');
            });
        Route::prefix('branch')
            ->controller(BranchController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::get('/datatable', 'datatable')->name('v1.branch.datatable');
                Route::get('/add', 'add');
                Route::get('/edit/{id}', 'edit');
                Route::get('/delete/{id}', 'delete');
                Route::post('/store', 'store');
                Route::post('/update/{id}', 'update');
            });
        Route::prefix('report-absensi')
            ->controller(AbsenController::class)->group(function () {
                Route::get('/', 'index');
                Route::get('/datatable', 'datatable')->name('v1.reportabsensi.datatable');
            });
    });
});
