<?php

use App\Http\Controllers\Admin\WordController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BabController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BaitController;
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

Route::prefix('datatable')->group(function () {
    Route::post('bab', [BabController::class, 'datatable'])->name('api.bab_datatable');
    Route::post('chapter', [ChapterController::class, 'datatable'])->name('api.chapter_datatable');
    Route::post('kata', [WordController::class, 'datatable'])->name('api.kata_post_datatable');
    Route::post('template', [WordTemplateController::class, 'datatable'])->name('api.word_template_datatable');
    Route::get('template/{id?}', [WordTemplateController::class, 'show'])->name('api.template_show');
});


Route::get('/login', [AuthController::class, 'loginView']);
Route::post('/login', [AuthController::class, 'login'])->name('admin.login');

Route::group(['middleware' => ['isAuth', 'isRole:admin']], function () {
    Route::resource('template', 'WordTemplateController')->except(['create', 'show']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
});

Route::group(['middleware' => ['isAuth', 'isRole:admin|penginput']], function () {
    Route::get('', [DashboardController::class, 'index'])->name('admin');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('category', CategoryController::class)->except(['create']);
    Route::post('datatable/category', [CategoryController::class, 'datatable'])->name('api.category_datatable');

    Route::get('kitab/{id?}', [BookController::class, 'index'])->name('book.index');
    Route::resource('book', BookController::class)->except(['index', 'create', 'show']);
    Route::get('kitab/{id}/show', [BookController::class, 'show'])->name('book.show');
    Route::post('datatable/book', [BookController::class, 'datatable'])->name('api.book_datatable');

    Route::get('detail/{id}', [BabController::class, 'ajax_post_detail'])->name('post.ajax.detail');

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
