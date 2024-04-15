<?php

use App\Http\Controllers\Admin\WordController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BabController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\ChapterController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\WordTemplateController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/login', [AuthController::class, 'loginView']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::group(['middleware' => ['isAuth', 'isRole:admin|penginput']], function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('kategori', CategoryController::class)->except(['create']);

    Route::resource('kitab', BookController::class)->except(['create']);

    Route::get('bab/{id?}', [BabController::class, 'index'])->name('bab.index');
    Route::get('bab/{bab}/show', [BabController::class, 'show'])->name('bab.show');
    Route::put('bab/sort/{id}', [BabController::class, 'sort'])->name('bab.sort');
    Route::resource('bab', BabController::class)->except(['index', 'show']);

    Route::get('chapter/{id?}', [ChapterController::class, 'index'])->name('chapter.index');
    Route::get('chapter/{id}/show', [ChapterController::class, 'show'])->name('chapter.show');
    Route::resource('chapter', ChapterController::class)->except(['create', 'index', 'show']);

    Route::get('word/{id?}', [WordController::class, 'index'])->name('word.index');
    Route::get('word/{chapter}/show', [WordController::class, 'show'])->name('word.show');
    Route::post('word/duplicate', [WordController::class, 'duplicate'])->name('word.duplicate');
    Route::patch('word/{id}/{type}', [WordController::class, 'update_number'])->name('word.patch');
    Route::resource('word', WordController::class)->except(['index', 'show']);
});
