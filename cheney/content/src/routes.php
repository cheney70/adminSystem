<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2026/1/1
 * Time: 13:59
 */

use Illuminate\Support\Facades\Route;

Route::prefix('/article')->group(function(){
    Route::get('/list','ArticleController@lists');
    Route::get('/tops','ArticleController@tops');
    Route::get('/detail/{id}','ArticleController@detail');
});

Route::prefix('/article-type')->group(function(){
    Route::get('/list','ArticleTypeController@lists');
    Route::get('/detail/{id}','ArticleTypeController@detail');
});