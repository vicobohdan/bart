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

Route::get('/gallery', 'Api\GalleryController@index');
Route::post('/gallery', 'Api\GalleryController@store');
Route::get('/gallery/{path}', 'Api\GalleryController@show');
Route::delete('/gallery/{path}/{file?}', 'Api\GalleryController@destroy');
Route::post('/gallery/{path}', 'Api\GalleryController@upload');

Route::get('/images/{w}x{h}/{gallery}/{image}', 'Api\ImageController@show')->where(['x' => '[0-9]+', 'y' => '[0-9]+']);