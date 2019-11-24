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




	/* Sign Up */ 
	Route::post('signUp', 'API\OAuthController@signUp');
	
	Route::post('signIn', 'API\OAuthController@signIn');
   

	Route::post('refreshToken', 'API\OAuthController@refreshToken');

	Route::group(['middleware' => 'auth:api'], function(){

			/* Sign Out */ 
			Route::post('signOut', 'API\OAuthController@signOut');
		
			Route::get('userProfile', 'API\UserController@userProfile');
			

	});
