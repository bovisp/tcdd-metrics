<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// badge languages
Route::post('badge-languages', 'BadgeLanguageController@store');

Route::delete('badge-languages/{badgeLanguage}', 'BadgeLanguageController@destroy');

Route::put('badge-languages/{badgeLanguage}', 'BadgeLanguageController@update');

Route::get('badge-languages', 'BadgeLanguageController@index');

// course languages
Route::post('course-languages', 'CourseLanguageController@store');

Route::delete('course-languages/{courseLanguage}', 'CourseLanguageController@destroy');

Route::put('course-languages/{courseLanguage}', 'CourseLanguageController@update');

Route::get('course-languages', 'CourseLanguageController@index');

// multilingual courses
Route::post('multilingual-courses', 'MultilingualCourseController@store');

Route::delete('multilingual-courses/{multilingualCourse}', 'MultilingualCourseController@destroy');

Route::put('multilingual-courses/{multilingualCourse}', 'MultilingualCourseController@update');

Route::get('multilingual-courses', 'MultilingualCourseController@index');

// multilingual course groups
Route::get('/multilingual-course-groups', 'MultilingualCourseGroupController@index');

Route::post('/multilingual-course-groups', 'MultilingualCourseGroupController@store');

Route::delete('/multilingual-course-groups/{multilingualCourseGroup}', 'MultilingualCourseGroupController@destroy');

Route::put('/multilingual-course-groups/{multilingualCourseGroup}', 'MultilingualCourseGroupController@update');

// reports
Route::post('reports', 'ReportController@store');

Route::get('reports', 'ReportController@index');

Route::get('reports/minDateTimestamps', 'ReportController@minDateTimestamp');

Route::get('test', 'ReportController@test');

//uploads
Route::post('comet-accesses', 'UploadController@storeAccesses');
Route::post('comet-completions', 'UploadController@storeCompletions');
Route::post('comet-uploads', 'UploadController@upload');

//show
//edit
//create