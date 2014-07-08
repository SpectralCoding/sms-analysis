<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/process/{chunksize}/{phone}', 'ProcessController@Process');
Route::get('/', 'MainController@Home');
Route::get('/resume/{userid}', 'MainController@Resume');
