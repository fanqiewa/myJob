<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Request;

class CommonController extends Controller
{
    /**
     * [图片上传]
     * @return [type] [description]
     */
    public function uploadImage () {
        if (Request::file('file')){
            $file = Request::file('file');
            $allowed_extensions = ['png', 'jpg', 'gif', 'pdf'];
            $uploadUrl = 'uploads/images/';
            if ($file->getClientOriginalExtension() == 'pdf') {
                $uploadUrl =  'uploads/pdf/';
            }
            if (!in_array($file->getClientOriginalExtension(), $allowed_extensions)) {
                return ajax_result(500, '只能上传png、jpg、gif文件');
            } else{
                $destinationPath = $uploadUrl; //public 文件夹下面建 $uploadUrl 文件夹
                $extension = $file->getClientOriginalExtension();
                $fileName = md5(time().rand(1,1000)).'.'.$extension;
                $file->move($destinationPath,$fileName);
                $filePath = asset($destinationPath.$fileName);
                $data = array(
                    'filename' => $fileName,
                    'filepath' => '/'.$destinationPath.$fileName,
                    'realpath' => $filePath
                );
                return ajax_result(200, '上传成功', $data);
            }
        } else {
            return ajax_result(500, '上传失败！');
        }
    }

}
