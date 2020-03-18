<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Request;
use Util;
use App\Models\Position\Position;

class PositionController extends Controller
{
    /**
     * [获取职位列表]
     * @return [type] [description]
     */
    public function getPositionList(){

        $result = Position::with(['children' => function ($query) {
            $query->with(['children' => function ($query) {
                $query->select('id', 'id as value', 'name as label', 'last_id');
            }]);
            $query->select('id', 'id as value', 'name as label', 'last_id');
        }])->where('last_id', 0)->select('id', 'id as value', 'name as label', 'last_id')->get()->toArray();

        $data = array(
            'list' =>$result,
            );
        return ajax_result(200,'ok',$data);
    }

    /**
     * [获取职位列表]
     * @return [type] [description]
     */
    public function getAllPositionList(){

        $result = Position::get()->toArray();

        $data = array(
            'list' =>$result,
            );
        return ajax_result(200,'ok',$data);
    }

}
