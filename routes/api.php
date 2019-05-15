<?php

use Illuminate\Http\Request;

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

Route::post('/register', 'Auth\AuthController@register');

Route::post('/login', 'Auth\AuthController@login');

Route::group(['middleware' => 'jwt.auth'], function(){
    Route::get('/me', 'Auth\AuthController@user');
    Route::get('/timeline', 'TimelineController@index');
    Route::post('/logout', 'Auth\AuthController@logout');
});

Route::post('/badge-languages', 'BadgeLanguageController@store');

Route::put('/badge-languages/{badgeLanguage}', 'BadgeLanguageController@update');

Route::delete('/badge-languages/{badgeLanguage}', 'BadgeLanguageController@destroy');

Route::get('/badge-languages', 'BadgeLanguageController@index');

Route::get('/languages', 'LanguageController@index');

Route::get('/multilingual-course-groups', 'MultilingualCourseGroupController@index');

Route::post('/multilingual-course-groups', 'MultilingualCourseGroupController@store');

Route::get('/badges', 'BadgeController@index');

Route::get('/courses', 'CourseController@index');

Route::post('course-languages', 'CourseLanguageController@store');

Route::delete('course-languages/{courseLanguage}', 'CourseLanguageController@destroy');

Route::put('course-languages/{courseLanguage}', 'CourseLanguageController@update');

Route::get('course-languages', 'CourseLanguageController@index');

Route::post('multilingual-courses', 'MultilingualCourseController@store');

Route::delete('multilingual-courses/{multilingualCourse}', 'MultilingualCourseController@destroy');

Route::put('multilingual-courses/{multilingualCourse}', 'MultilingualCourseController@update');

Route::get('multilingual-courses', 'MultilingualCourseController@index');

// reports
Route::post('reports', 'ReportController@store');

Route::get('reports', 'ReportController@index');

Route::get('reports/minDateTimestamps', 'ReportController@minDateTimestamp');

//uploads
Route::post('store', 'UploadController@store');
Route::post('upload', 'UploadController@upload');

// Route::post('/auth/register', 'AuthController@register');
// Route::post('/auth/login', 'AuthController@login');
// Route::post('/auth/forgotpassword', 'PasswordResetController@email');
// Route::post('/auth/resetpassword/{token}', 'PasswordResetController@reset');
// Route::group(['middleware' => 'jwt.auth'], function(){
//   Route::get('auth/user', 'AuthController@user');
//   Route::post('auth/logout', 'AuthController@logout');
//   Route::group(['middleware' => ['role:admin']], function(){
//   	Route::resource('/manager', 'IndexController');
//   });
// });
// Route::group(['middleware' => 'jwt.refresh'], function(){
//   Route::get('auth/refresh', 'AuthController@refresh');
// });