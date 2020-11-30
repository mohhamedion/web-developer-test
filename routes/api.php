<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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




Route::group(['prefix'=>'v1'],function()
{


        Route::group(['prefix'=>'categories'],function()
        {
            Route::get('/', 'CategoryController@index');
            Route::group(['middleware'=>'auth:api'],function(){
                Route::post('/', 'CategoryController@store');
                Route::patch('/{category_id}', 'CategoryController@update');
                Route::delete('/{category_id}', 'CategoryController@destroy');
            });
        });



        Route::group(['prefix'=>'products'],function()
        {
            Route::get('/', 'ProductController@index');
            Route::get('/getProductsByCategoryId/{category_id}', 'ProductController@getProductsByCategoryId');
            Route::get('/{product_id}', 'ProductController@show');
            Route::group(['middleware'=>'auth:api'],function(){
                Route::post('/', 'ProductController@store');
                Route::patch('/{product_id}', 'ProductController@update');
                Route::delete('/{product_id}', 'ProductController@destroy');   
            });
        });


    Route::post('register','UserController@register');
    Route::post('login','UserController@login');
 
});