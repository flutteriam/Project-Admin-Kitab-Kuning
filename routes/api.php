<?php
/*
  Authors : initappz (Rahul Jograna)
  Website : https://initappz.com/
  App Name : Book Full App (InitBook)
  This App Template Source code is licensed as per the
  terms found in the Website https://initappz.com/license
  Copyright and Good Faith Purchasers © 2021-present initappz.
*/

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JWTMiddleware;
use App\Http\Middleware\AdminAuthMiddleware;
use App\Http\Controllers\api\v1\BabController;
use App\Http\Controllers\api\v1\FcmController;
use App\Http\Controllers\api\v1\OtpController;
use App\Http\Controllers\api\v1\BookController;
use App\Http\Controllers\api\v1\WordController;
use App\Http\Controllers\api\v1\UsersController;
use App\Http\Controllers\api\v1\ChapterController;
use App\Http\Controllers\api\v1\CommentsController;
use App\Http\Controllers\api\v1\Auth\AuthController;
use App\Http\Controllers\api\v1\BookLikesController;
use App\Http\Controllers\api\v1\SavedBookController;
use App\Http\Controllers\api\v1\CategoriesController;
use App\Http\Controllers\api\v1\Auth\LogoutController;
use App\Http\Controllers\api\v1\Auth\RegisterController;
use App\Http\Controllers\api\v1\Profile\ProfileController;
use App\Http\Controllers\api\v1\Auth\ResetPasswordController;
use App\Http\Controllers\api\v1\Auth\VerifyAccountController;
use App\Http\Controllers\api\v1\Auth\ForgotPasswordController;
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


Route::get('/', function () {
    return [
        'app' => 'Book App API by BunchDevelopers',
        'version' => '1.0.0',
    ];
});

Route::prefix('/v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);

        Route::post('create_account', [RegisterController::class, 'register']);
        Route::post('create_admin_account', [RegisterController::class, 'create_admin_account']);

        // Send reset password mail
        Route::post('recovery', [ForgotPasswordController::class, 'sendPasswordResetLink']);
        // handle reset password form process
        Route::post('reset', [ResetPasswordController::class, 'callResetPassword']);
        // handle reset password form process
        Route::post('verify', [VerifyAccountController::class, 'verify']);
    });

    Route::group(['middleware' => [JWTMiddleware::class]], function () {

        Route::prefix('profile')->group(function () {
            Route::get('', [ProfileController::class, 'me']);
            Route::post('update', [ProfileController::class, 'updateProfile']);
            Route::post('password', [ProfileController::class, 'updatePassword']);
            Route::post('getProfileById', [ProfileController::class, 'getProfileById']);
            Route::post('byId', [ProfileController::class, 'getById']);
        });
        Route::post('validate', [ProfileController::class, 'validate_user']);

        Route::prefix('auth')->group(function () {
            Route::post('logout', [LogoutController::class, 'logout']);
        });

        Route::post('books/saveLike', [BookLikesController::class, 'save']);
        Route::post('books/deleteLike', [BookLikesController::class, 'delete']);
        Route::post('books/saveBook', [SavedBookController::class, 'save']);
        Route::post('books/deleteSaved', [SavedBookController::class, 'delete']);
        Route::post('books/getSavedBooks', [SavedBookController::class, 'getSavedBook']);

        Route::post('comment/create', [CommentsController::class, 'save']);
        Route::post('comment/getById', [CommentsController::class, 'getById']);
        Route::post('comment/update', [CommentsController::class, 'update']);
        Route::post('comment/destroy', [CommentsController::class, 'delete']);
        Route::get('comment/getAll', [CommentsController::class, 'getAll']);

        Route::post('likes/getByBookId', [BookLikesController::class, 'getByBookId']);
    });

    Route::get('users/get_admin', [ProfileController::class, 'get_admin']);
    Route::get('users/get_admin_account', [ProfileController::class, 'get_admin_account']);
    Route::post('uploadImage', [ProfileController::class, 'uploadImage']);

    Route::post('users/emailExist', [ProfileController::class, 'emailExist']);

    Route::group(['middleware' => [AdminAuthMiddleware::class, JWTMiddleware::class]], function () {

        // CategoriesController Routes
        Route::post('categories/create', [CategoriesController::class, 'save']);
        Route::post('categories/getById', [CategoriesController::class, 'getById']);
        Route::post('categories/update', [CategoriesController::class, 'update']);
        Route::post('categories/destroy', [CategoriesController::class, 'delete']);
        Route::post('categories/updateOrder', [CategoriesController::class, 'updateOrder']);
        Route::get('categories/getAll', [CategoriesController::class, 'getAll']);

        // BookController
        Route::post('books/create', [BookController::class, 'save']);
        Route::post('books/getById', [BookController::class, 'getById']);
        Route::post('books/update', [BookController::class, 'update']);
        Route::post('books/destroy', [BookController::class, 'delete']);
        Route::get('books/getAll', [BookController::class, 'getAll']);
        Route::get('books/getHomeData', [BookController::class, 'getHomeData']);

        // BabController
        Route::post('babs/create', [BabController::class, 'save']);
        Route::post('babs/getById', [BabController::class, 'getById']);
        Route::post('babs/getById', [BabController::class, 'getById']);
        Route::post('babs/update', [BabController::class, 'update']);
        Route::post('babs/destroy', [BabController::class, 'delete']);
        Route::get('babs/getAll', [BabController::class, 'getAll']);
        Route::post('babs/updateOrder', [BabController::class, 'updateOrder']);
        Route::post('babs/getByBookId', [BabController::class, 'getByBookId']);

        // ChapterController
        Route::post('chapters/create', [ChapterController::class, 'save']);
        Route::post('chapters/getById', [ChapterController::class, 'getById']);
        Route::post('chapters/update', [ChapterController::class, 'update']);
        Route::post('chapters/destroy', [ChapterController::class, 'delete']);
        Route::get('chapters/getAll', [ChapterController::class, 'getAll']);
        Route::post('chapters/updateOrder', [ChapterController::class, 'updateOrder']);
        Route::post('chapters/getByBabId', [ChapterController::class, 'getByBabId']);

        // WordController
        Route::post('words/create', [WordController::class, 'save']);
        Route::post('words/getById', [WordController::class, 'getById']);
        Route::post('words/update', [WordController::class, 'update']);
        Route::post('words/destroy', [WordController::class, 'delete']);
        Route::get('words/getAll', [WordController::class, 'getAll']);
        Route::post('words/updateOrder', [WordController::class, 'updateOrder']);
        Route::post('words/getByChapterId', [WordController::class, 'getByChapterId']);
        Route::post('words/sort/{id}', [WordController::class, 'sort']);

        Route::get('users/getAll', [UsersController::class, 'getAll']);
    });

    Route::post('updateUserPasswordWithEmail', [ProfileController::class, 'updateUserPasswordWithEmail']);
    Route::post('sendNoficationGlobal', [ProfileController::class, 'sendNoficationGlobal']);
    Route::post('saveToken', [FcmController::class, 'saveToken']);
    Route::get('categories/getCategoriesForUser', [CategoriesController::class, 'getCategoriesForUser']);

    Route::post('uploadVideo', [BookController::class, 'uploadVideo']);
    Route::post('books/getByCate', [BookController::class, 'getByCate']);
    Route::post('books/getRelate', [BookController::class, 'getRelate']);
    Route::post('books/getBySlugs', [BookController::class, 'getBySlugs']);
    Route::post('books/getByBookId', [BookController::class, 'getByBookId']);
    Route::post('books/getById', [BookController::class, 'getById']);
    Route::post('books/searchQuery', [BookController::class, 'searchQuery']);
    Route::get('books/getVideoBook', [BookController::class, 'getVideoBook']);

    Route::post('otp/verifyOTP', [OtpController::class, 'verifyOTP']);
    Route::post('updateUserPasswordWithEmail', [ProfileController::class, 'updateUserPasswordWithEmail']);

    Route::post('comment/getByBookId', [CommentsController::class, 'getByBookId']);
});
