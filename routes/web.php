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
    return redirect('index');
});

/**
 * 首页
 */
Route::any('index', function () {
    return view('index');
});


// 前台
Route::group(['middleware' => ['web']], function () {

    // 用户
    Route::any('/api/Front/login', 'Front\UserController@login'); // 用户注册
    Route::any('/api/Front/register', 'Front\UserController@register'); // 用户登录
    Route::any('/api/Front/uploadImage', 'Front\CommonController@uploadImage'); // 图片上传接口
    Route::any('getUserList', 'Front\UserController@getUserList'); // 获取用户列表
});
