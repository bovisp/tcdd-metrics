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

Route::post('badge-languages', 'BadgeLanguageController@store');

Route::delete('badge-languages/{badgeLanguage}', 'BadgeLanguageController@destroy');

Route::put('badge-languages/{badgeLanguage}', 'BadgeLanguageController@update');

Route::get('badge-languages', 'BadgeLanguageController@index');


Route::post('course-languages', 'CourseLanguageController@store');

Route::delete('course-languages/{courseLanguage}', 'CourseLanguageController@destroy');

Route::put('course-languages/{courseLanguage}', 'CourseLanguageController@update');

Route::get('course-languages', 'CourseLanguageController@index');

//show
//edit
//create