<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Request;
use App\Models\User\User;

class UserController extends Controller
{
    /**
     * [用户登录]
     * @return [type] [description]
     */
    public function login () {
        $account = Request::input('account');
        $password = Request::input('password');
        if (empty($account) || empty($password)) {
            return ajax_result(500, '传参有误！');
        }
        $result = User::where([
            ['account', $account],
            ['password', $password]
        ])->get();
        if ($result->isEmpty()) {
            return ajax_result(500, '用户不存在！');
        }
        return ajax_result(200, 'ok', $result);
    }

    /**
     * [用户登录]
     * @return [type] [description]
     */
    public function register () {
        $account = Request::input('account');
        $password = Request::input('password');
        if (empty($account) || empty($password)) {
            return ajax_result(500, '传参有误！');
        }
        $result = User::where('account', $account)->get();
        if (!$result->isEmpty()) {
            return ajax_result(500, '该账户已存在！请重新注册');
        }
        $user = User::create(array(
            'account' => $account,
            'password' => $password,
        ));
        unset($user->id);
        return ajax_result(200, 'ok', $user);
    }

    /**
     * [用户列表]
     * @return [type] [description]
     */
    public function getUserList () {
        $result = User::get()->toArray();
        $data = array(
            'list' =>$result,
            );
        return ajax_result(200,'ok',$data);
    }
}
