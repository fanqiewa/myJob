<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Response;
use Request;
use App\Models\User\User;
use App\Models\User\JobHunter;

class CommonController extends Controller
{
    /**
     * [图片上传]
     * @return [type] [description]
     */
    public function uploadFile () {
        $account = Request::input('account');
        if (empty($account)) {
            return ajax_result(500,'参数有误');
        }
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
                $pdf_name = Request::input('pdf_name');
                $pdf_update = Request::input('pdf_update');
                $user_id = User::where('account', $account)->value('id');
                JobHunter::updateOrCreate(
                    ['user_id' => $user_id],
                    ['user_id' => $user_id, 'pdf_name' => $pdf_name, 'pdf_update' => $pdf_update, 'pdf' => '/'.$destinationPath.$fileName]
                );
                
                return ajax_result(200, '上传成功', $data);
            }
        } else {
            return ajax_result(500, '上传失败！');
        }
    }

    /**
     * [文件下载]
     * @return [type] [description]
     */
    public function down () {

 
        $file= "uploads/pdf/4b955388db67d1336e33dcc1e89d2aa6.pdf";
        $headers = array(
            'Content-Type: application/pdf',
        );

        return Response::download($file, 'filename.pdf', $headers);
    }

    
}
