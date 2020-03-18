<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Request;
use Util;
use App\Models\User\User;
use App\Models\User\JobHunter;
use App\Models\User\Salary;
use App\Models\User\Experience;
use App\Models\User\JobHunterPosition;

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
        return ajax_result(200, 'ok', $data);
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

        $result = JobHunter::with('experience', 'position')->where('user_id', $user_id)->first();

        $data = array(
            'result' =>$result,
            );
        return ajax_result(200, 'ok', $data);
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
        return ajax_result(200, 'ok', $data);
    }

    /**
     * [获取薪资列表]
     * @return [type] [description]
     */
    public function getSalaryList () {
        $result = Salary::get();

        $data = array(
            'list' =>$result,
            );
        return ajax_result(200, 'ok', $data);
    }

    
    /**
     * [更新工作经历]
     * @return [type] [description]
     */
    public function updateWork () {
        $jobhunter_id = Request::input('jobhunter_id');
        $experience_id = Request::input('experience_id');
        $experience_json = Request::input('experience_json');
        
        $result = Experience::updateOrCreate(
            ['jobhunter_id' => $jobhunter_id, 'id' => $experience_id],
            $experience_json
        );
        $data = array(
            'result' =>$result,
            );
        return ajax_result(200, 'ok', $data);
    }

    /**
     * [删除工作经历]
     * @return [type] [description]
     */
    public function deleteWork () {
        $experience_id = Request::input('experience_id');
        
        $result = Experience::where('id', $experience_id)->delete();

        return ajax_result(200, 'ok', $result);
    }

    
    /**
     * [更新职位经历信息]
     * @return [type] [description]
     */
    public function updatePosition () {
        $jobhunter_id = Request::input('jobhunter_id');
        $jobhunter_position_id = Request::input('jobhunter_position_id');
        $position_json = Request::input('position_json');
        $position_json['position_id'] = json_encode($position_json['position_id'] ?? []);
        $position_json['city_id'] = json_encode($position_json['city_id'] ?? []);
        
        $result = JobHunterPosition::updateOrCreate(
            ['jobhunter_id' => $jobhunter_id, 'id' => $jobhunter_position_id],
            $position_json
        );
        $data = array(
            'result' =>$result,
            );
        return ajax_result(200, 'ok', $data);
    }

    /**
     * [删除工作经历]
     * @return [type] [description]
     */
    public function deletePosition () {
        $jobhunter_position_id = Request::input('jobhunter_position_id');
        
        $result = JobHunterPosition::where('id', $jobhunter_position_id)->delete();

        return ajax_result(200, 'ok', $result);
    }
}
