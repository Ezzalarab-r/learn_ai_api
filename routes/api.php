<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['namespace' => 'Api'], function () {

    Route::post(
        '/auth/login',
        'UserController@signIn',
    );

    Route::group(['middleware' => 'auth:sanctum'], function () {
        // Route::get('/courseList', [CourseController::class, 'courseList']);
        Route::any('/coursesList', 'CourseController@coursesList');
        Route::any('/courseDetails', 'CourseController@courseDetails');
        Route::any('/checkout', 'PaymentController@checkout');
    });
});
