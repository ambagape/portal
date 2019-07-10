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

Route::namespace('API\v1')->prefix('auth')->group(function () {
    Route::post('login', ['uses' => 'AuthController@Login', 'as' => 'auth.login']);
});

Route::namespace('API\v1')->prefix('chat')->middleware(['auth:api'])->group(function () {
    Route::get('conversations', ['uses' => 'ChatController@conversations', 'as' => 'chat.conversations']);
    Route::post('conversations', ['uses' => 'ChatController@startConversation', 'as' => 'chat.startConversation']);
    Route::get('messages/{chat_conversation}', ['uses' => 'ChatController@messages', 'as' => 'chat.messages']);
    Route::post('messages/{chat_conversation}', ['uses' => 'ChatController@sendMessage', 'as' => 'chat.sendMessage']);
});

Route::namespace('API\v1')->prefix('image')->group(function () {
    Route::get('profile/{id}', ['uses' => 'ImageController@showProfilePicture', 'as' => 'image.show-profile-picture']);
    Route::get('profile/delete/{id}', ['uses' => 'ImageController@clearProfilePicture', 'as' => 'image.clear-profile-picture']);
    Route::post('profile', ['uses' => 'ImageController@uploadProfilePicture', 'as' => 'image.upload-profile-picture']);
});
