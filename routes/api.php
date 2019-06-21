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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::namespace('API\v1')->prefix('image')->group(function () {
    Route::get('profile/{id}', ['uses' => 'ImageController@showProfilePicture', 'as' => 'image.show-profile-picture']);
    Route::post('profile', ['uses' => 'ImageController@uploadProfilePicture', 'as' => 'image.upload-profile-picture']);
});
