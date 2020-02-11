<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User\User;

class UserController extends Controller
{
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
