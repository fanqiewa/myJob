<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Request;
use Util;
use App\Models\User\User;
use App\Models\User\JobHunter;

class UserController extends Controller
{
    /**
     * [用户登录]
     * @return [type] [description]
     */
    public function login () {
        $account = trim(Request::input('account'));
        $password = trim(Request::input('password'));

        if (empty($account) || empty($password)) {
            return ajax_result(500, '传参有误！');
        }
        
        $user = User::where([
            ['account', $account],
            ['password', $password]
        ])->first();


        if (empty($user)) {
            return ajax_result(500, '用户不存在！');
        }
        
        $token = Util::genToken();
        $user->webtoken = $token;
        $user->save();
        
        $data = array(
            'user' => $user
        );
        return ajax_result(200, 'ok', $data);
    }

    /**
     * [用户注册]
     * @return [type] [description]
     */
    public function register () {
        $account = trim(Request::input('account'));
        $password = trim(Request::input('password'));
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
        $token = Util::genToken();
        $user->webtoken = $token;
        $user->save();
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

    /**
     * [获取求职值信息]
     * @return [type] [description]
     */
    public function getJobHunterInfo () {
        $account = Request::input('account');
        if (empty($account)) {
            return ajax_result(500,'参数有误');
        }
        $user_id = User::where('account', $account)->value('id');

        $result = JobHunter::where('user_id', $user_id)->first();

        $data = array(
            'result' =>$result,
            );
        return ajax_result(200,'ok',$data);
    }

    /**
     * [更新求职值信息]
     * @return [type] [description]
     */
    public function updateJobHunterInfo () {
        $account = Request::input('account');
        if (empty($account)) {
            return ajax_result(500,'参数有误');
        }
        $user_id = User::where('account', $account)->value('id');

        $user_json = Request::input('user_json');
        $user_json['user_id'] = $user_id;
        $result = JobHunter::updateOrCreate(
            ['user_id' => $user_id],
            $user_json
        );
        $data = array(
            'result' =>$result,
            );
        return ajax_result(200,'ok',$data);
    }
}
