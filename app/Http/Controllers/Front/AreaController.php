<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Request;
use Util;
use App\Models\Area\Area;

class AreaController extends Controller
{
    /**
     * [获取区域列表]
     * @return [type] [description]
     */
    public function getAreaList(){

        $result = Area::with(['children' => function ($query) {
            $query->select('id', 'id as value', 'name as label', 'last_id');
        }])->where('last_id', 0)->select('id', 'id as value', 'name as label', 'last_id')->get()->toArray();

        $data = array(
            'list' =>$result,
            );
        return ajax_result(200,'ok',$data);
    }

    /**
     * [获取区域列表]
     * @return [type] [description]
     */
    public function getAllAreaList(){

        $result = Area::get()->toArray();

        $data = array(
            'list' =>$result,
            );
        return ajax_result(200,'ok',$data);
    }

}
