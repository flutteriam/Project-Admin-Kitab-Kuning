<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BabController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BaitController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\NahwuController;
use App\Http\Controllers\Admin\TarqibController;
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
    Route::post('bait', [BaitController::class, 'datatable'])->name('api.bait_datatable');
    Route::post('kata', [ChapterController::class, 'datatable'])->name('api.kata_post_datatable');
    Route::post('tag', [TagController::class, 'datatable'])->name('api.tag_datatable');
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

    Route::get('bait/{id?}', [BaitController::class, 'index'])->name('bait.index');
    Route::get('bait/{id}/show', [BaitController::class, 'show'])->name('bait.show');
    Route::resource('bait', BaitController::class)->except(['create', 'index', 'show']);

    Route::get('chapter/{id?}', [ChapterController::class, 'index'])->name('chapter.index');
    Route::get('chapter/{bait}/show', [ChapterController::class, 'show'])->name('chapter.show');
    Route::post('chapter/duplicate', [ChapterController::class, 'duplicate'])->name('chapter.duplicate');
    Route::patch('chapter/{id}/{type}', [ChapterController::class, 'update_number'])->name('chapter.patch');
    Route::resource('chapter', ChapterController::class)->except(['index', 'show']);
});

Route::group(['middleware' => ['isAuth', 'isRole:admin|contributor']], function () {
    Route::resource('tag', TagController::class)->except(['create', 'show']);
    Route::get('tag/show', [TagController::class, 'show'])->name('tag.show');

    Route::get('post/{id}/show', [BookController::class, 'show'])->name('post.show');

    Route::get('bab/{bab}/show', [BabController::class, 'show'])->name('bab.show');

    Route::get('nahwu/{id?}', [NahwuController::class, 'index'])->name('nahwu.index');
    Route::get('nahwu/{bait}/show', [NahwuController::class, 'show'])->name('nahwu.show');
    Route::resource('nahwu', NahwuController::class)->except(['index', 'show', 'destroy']);
    Route::get('nahwu/bab/{id}', [NahwuController::class, 'ajax_bab_detail'])->name('nahwu.bab.ajax.detail');
    Route::put('nahwu/verified/update', [NahwuController::class, 'updateVerified'])->name('nahwu.verified');

    Route::get('tarqib/{id?}', [TarqibController::class, 'index'])->name('tarqib.index');
    Route::get('tarqib/{bait}/show', [TarqibController::class, 'show'])->name('tarqib.show');
    Route::resource('tarqib', 'TarqibController')->except(['index', 'show']);
    Route::get('tarqib/bab/{id}', [TarqibController::class, 'ajax_bab_detail'])->name('tarqib.bab.ajax.detail');
});
