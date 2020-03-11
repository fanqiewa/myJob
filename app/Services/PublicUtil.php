<?php
namespace App\Services;

use Storage;
use Log;
use Session;
use Config;
use Qiniu\Auth as qiniuAuth;
use Qiniu\Storage\BucketManager;
use Event;
use EasyWeChat\Factory;
use PHPExcel;
use PHPExcel_Reader_Excel2007;
use PHPExcel_Reader_Excel5;
class PublicUtil{

    public function __construct()
    {
    }
// ======================以下是微信通用的API方法==================

    /**
     * [app 微信应用实例]
     * @return [type] [description]
     */
    public function app() {

        $wechat = Config::get('wechat.official_account.default');
        if(empty($wechat['app_id']) || empty($wechat['secret']) ||empty($wechat['token'])) {
            return false;
        }
        $options = [
          'app_id'  => $wechat['app_id'],
          'secret'  => $wechat['secret'],
          'token'   => $wechat['token'],
          'aes_key' => $wechat['aes_key'],
          'oauth' => [
              'scopes'   => ['snsapi_userinfo'],
              'callback' => "auth",
          ]
        ];
        $app = Factory::officialAccount($options);
        return $app;
    }

    // 生成OAuth2的Access Token
    public function oauth2_access_token($code) {
        $wechat = Config::get('wechat');
        $default = $wechat['official_account']['default'];
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$default['app_id']."&secret=".$default['secret']."&code=".$code."&grant_type=authorization_code";
        $res = $this->http_request($url);
        return json_decode($res, true);
    }


    // HTTP请求（支持HTTP/HTTPS，支持GET/POST）
    protected function http_request($url, $data = null) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    /**
     * [jssdk 微信js接口]
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public function jssdk($url=null) {
        $app=$this->app();
        if(!empty($url)){
          $app->js->setUrl($url);
        }
        // 获取微信分享jssdk
        $APIs = array('onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','hideMenuItems','showMenuItems','hideAllNonBaseMenuItem','showAllNonBaseMenuItem','translateVoice','startRecord','stopRecord','onRecordEnd','playVoice','pauseVoice','stopVoice','uploadVoice','downloadVoice','chooseImage','previewImage','uploadImage','downloadImage','getNetworkType','openLocation','getLocation','hideOptionMenu','showOptionMenu','closeWindow','scanQRCode','chooseWXPay','openProductSpecificView','addCard','chooseCard','openCard');
        $jssdk = $app->js->config($APIs, $debug = false, $beta = false, $json = false);
        return $jssdk;
    }

// ======================以下是七牛通用的API方法===============
    /**
     * [upload_chat 上传图片到七牛]
     * @param  [type] $mediaId [description]
     * @return [type]          [description]
     */
    public function upload_chat($mediaId){
        if(empty($mediaId)){
            return false;
        }
        $token = $this->app()->access_token->getToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$token.'&media_id='.$mediaId;

        $qiniuConfig = Config::get('base.qiniu');
        $auth = new qiniuAuth($qiniuConfig['access_key'], $qiniuConfig['secret_key']);
        $bucketMgr = new BucketManager($auth);

        $bucket = $qiniuConfig['bucket'];
        $random = $this->generate_password(8);
        $key = $qiniuConfig['chat_prefix'].$random.time().'.jpg';
        list($ret, $err) = $bucketMgr->fetch( $url, $bucket, $key );
        if ($err !== null) {
            return false;
        }
        $img = 'http://'.$qiniuConfig['domains']['custom'].'/'.$key;
        return $img;
    }

    /**
     * [upload_avatar 上传头像到七牛]
     * @param  [type] $mediaId [description]
     * @return [type]          [description]
     */
    public function upload_avatar($url,$openid){
        if(empty($url) || empty($openid)){
            return false;
        }
        $qiniuConfig = Config::get('base.qiniu');
        $auth = new qiniuAuth($qiniuConfig['access_key'], $qiniuConfig['secret_key']);
        $bucketMgr = new BucketManager($auth);

        $bucket = $qiniuConfig['bucket'];
        $key = $qiniuConfig['avatar_prefix'].$openid.'.jpg';
        list($ret, $err) = $bucketMgr->fetch($url,$bucket,$key);
        if ($err !== null) {
            return false;
        }
        $img = 'http://'.$qiniuConfig['domains']['custom'].'/'.$key;
        return $img;
    }

    /**
     * [upload_ 上传各种图片到七牛]
     * @param  [type] $mediaId [description]
     * @return [type]          [description]
     */
    public function upload_image($mediaId){
        if(empty($mediaId)){
            return false;
        }
        $token = $this->app()->access_token->getToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$token.'&media_id='.$mediaId;

        $qiniuConfig = Config::get('base.qiniu');
        $auth = new qiniuAuth($qiniuConfig['access_key'], $qiniuConfig['secret_key']);
        $bucketMgr = new BucketManager($auth);

        $bucket = $qiniuConfig['bucket'];
        $random = $this->generate_password(8);
        $key = $qiniuConfig['images_prefix'].$random.time().'.jpg';
        list($ret, $err) = $bucketMgr->fetch( $url, $bucket, $key );
        if ($err !== null) {
            return false;
        }
        $img = 'http://'.$qiniuConfig['domains']['custom'].'/'.$key;
        return $img;
    }

    /**
     * [upload_ 上传各种图片到七牛]
     * @param  [type] $mediaId [description]
     * @return [type]          [description]
     */
    public function upload_photo ($url, $openid){
        if(empty($url)){
            return false;
        }
        $qiniuConfig = Config::get('base.qiniu');
        $auth = new qiniuAuth($qiniuConfig['access_key'], $qiniuConfig['secret_key']);
        $bucketMgr = new BucketManager($auth);

        $bucket = $qiniuConfig['bucket'];
        $random = $this->generate_password(6);
        $key = $qiniuConfig['images_prefix'].$openid.$random.time().'.jpg';
        list($ret, $err) = $bucketMgr->fetch( $url, $bucket, $key );
        if ($err !== null) {
            return false;
        }
        $img = 'http://'.$qiniuConfig['domains']['custom'].'/'.$key;
        return $img;
    }

    /**
     * [save2path 保存头像到本地]
     * @param  [type] $url    [description]
     * @param  [type] $openid [description]
     * @return [type]         [description]
     */
    public function save2path($avatar,$openid=''){
        $ch = curl_init($avatar) ;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
        $output = curl_exec($ch) ;

        if(empty($openid)){ // 保存默认头像到本地
            $name = 'nunu';
        }else{ // 保存指定头像到本地
            $name = $openid;
        }
        if (Storage::put($name.'.jpg', $output)) {
            return storage_path().'/app/'.$name.'.jpg';
        }
        return false;
    }

// =========================下面是bearychat（推送错误）通用方法==================
    /**
     * [bearychat 推送错误]
     * @param  [type] $e [description]
     * @return [type]    [description]
     */
    public function bearychat($e){
        if(!empty($e)){
            bearychat()->text('New Exception: '.get_class($e))
            ->notification('New Exception: '.get_class($e))
            ->add('URL:'.app('request')->fullUrl())
            ->add('UserAgent:'.app('request')->server('HTTP_USER_AGENT'))
            ->add('IP:'.app('request')->server('REMOTE_ADDR'))
            ->add('openid:'.(Session::get('openid')))
            ->add(['text'=>$e->getFile().' [错误行数]'.$e->getLine(),'color' => '#f00'])
            ->add(['text'=>$e->getMessage(),'color' => '#f00'])
            ->add($e)->send();
        }
    }



// ========================下面是通用方法==================================


    /**
     * 发起http请求
     * @param string $url 访问路径
     * @param array $params 参数，该数组多于1个，表示为POST
     * @param int $expire 请求超时时间
     * @param array $extend 请求伪造包头参数
     * @param string $hostIp HOST的地址
     * @return array    返回的为一个请求状态，一个内容
     */
    public function makeRequest($url, $params = array(), $expire = 0, $extend = array(), $hostIp = '')
    {
        if (empty($url)) {
            return array('code' => '100');
        }
        $_curl = curl_init();
        $_header = array(
            'Accept-Language: zh-CN',
            'Connection: Keep-Alive',
            'Cache-Control: no-cache',
            'Content-Type: application/x-www-form-urlencoded'
        );
        // 方便直接访问要设置host的地址
        if (!empty($hostIp)) {
            $urlInfo = parse_url($url);
            if (empty($urlInfo['host'])) {
                $urlInfo['host'] = substr(DOMAIN, 7, -1);
                $url = "http://{$hostIp}{$url}";
            } else {
                $url = str_replace($urlInfo['host'], $hostIp, $url);
            }
            $_header[] = "Host: {$urlInfo['host']}";
        }
        // 只要第二个参数传了值之后，就是POST的
        if (!empty($params)) {
            curl_setopt($_curl, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($_curl, CURLOPT_POST, true);
        }
        if (substr($url, 0, 8) == 'https://') {
            curl_setopt($_curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($_curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        curl_setopt($_curl, CURLOPT_URL, $url);
        curl_setopt($_curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($_curl, CURLOPT_USERAGENT, 'API PHP CURL');
        curl_setopt($_curl, CURLOPT_HTTPHEADER, $_header);
        if ($expire > 0) {
            curl_setopt($_curl, CURLOPT_TIMEOUT, $expire); // 处理超时时间
            curl_setopt($_curl, CURLOPT_CONNECTTIMEOUT, $expire); // 建立连接超时时间
        }
        // 额外的配置
        if (!empty($extend)) {
            curl_setopt_array($_curl, $extend);
        }
        $result['result'] = curl_exec($_curl);
        $result['code'] = curl_getinfo($_curl, CURLINFO_HTTP_CODE);
        $result['info'] = curl_getinfo($_curl);
        if ($result['result'] === false) {
            $result['result'] = curl_error($_curl);
            $result['code'] = -curl_errno($_curl);
        }
        curl_close($_curl);
        return $result;
    }


    public function makeAliRequest($method,$host,$path,$headers) {
        $method = strtoupper($method);
        $url = $host . $path;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        if($method == 'POST'){
            $index = strpos($path,'?');
            if ($index !== false) {
                $bodys = substr($path,$index+1);
            }
            curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
        }
        $result['code'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result['data'] = curl_exec($curl);

        return $result;
    }

    /**
     * [genToken 获取token]
     * @param  integer $len [description]
     * @param  boolean $md5 [description]
     * @return [type]       [description]
     */
    public function genToken( $len = 32, $md5 = true ) {
        mt_srand( (double)microtime()*1000000 );
        $chars = array(
          'Q', '@', '8', 'y', '%', '^', '5', 'Z', '(', 'G', '_', 'O', '`',
          'S', '-', 'N', '<', 'D', '{', '}', '[', ']', 'h', ';', 'W', '.',
          '/', '|', ':', '1', 'E', 'L', '4', '&', '6', '7', '#', '9', 'a',
          'A', 'b', 'B', '~', 'C', 'd', '>', 'e', '2', 'f', 'P', 'g', ')',
          '?', 'H', 'i', 'X', 'U', 'J', 'k', 'r', 'l', '3', 't', 'M', 'n',
          '=', 'o', '+', 'p', 'F', 'q', '!', 'K', 'R', 's', 'c', 'm', 'T',
          'v', 'j', 'u', 'V', 'w', ',', 'x', 'I', '$', 'Y', 'z', '*'
        );
        # Array indice friendly number of chars;
        $numChars = count($chars) - 1; $token = '';
        # Create random token at the specified length
        for ( $i=0; $i<$len; $i++ )
          $token .= $chars[ mt_rand(0, $numChars) ];
        # Should token be run through md5?
        if ( $md5 ) {
          # Number of 32 char chunks
          $chunks = ceil( strlen($token) / 32 ); $md5token = '';
          # Run each chunk through md5
          for ( $i=1; $i<=$chunks; $i++ )
              $md5token .= md5( substr($token, $i * 32 - 32, 32) );
          # Trim the token
          $token = substr($md5token, 0, $len);
        } return $token;
    }

    /**
     * [generate_password 随机字符]
     * @param  integer $length [description]
     * @return [type]          [description]
     */
    public function generate_password( $length = 32 ) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ( $i = 0; $i < $length; $i++ )
        {
            $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }
        return $password;
    }

    /**
     * [generate_password 纯数字]
     * @param  integer $length [description]
     * @return [type]          [description]
     */
    public function generate_number( $length = 6 ) {
        $chars = '0123456789';
        $password = '';
        for ( $i = 0; $i < $length; $i++ )
        {
            $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }
        return $password;
    }

    /**
     * [export 导出数据]
     * @param  [type] $head     [标题]
     * @param  [type] $body     [内容]
     * @param  string $fileName [文件名称]
     * @return [type]           [description]
     */
    public function export($head,$body,$fileName = '数据'){
        if (empty($head)) {
            return;
        }
        header ( 'Content-Type: text/html;charset=utf-8' );
        header ( 'Content-Type: application/vnd.ms-excel' );
        header ( 'Content-Disposition: attachment;filename="'.$fileName.date("Y-m-d h:i:sa").'.csv"');
        header ( 'Cache-Control: max-age=0' );
        // 打开PHP文件句柄，php://output 表示直接输出到浏览器
        $fp = fopen ( 'php://output', 'a' );
        // 输出Excel列名信息
        foreach ( $head as $i => $v ) {
          // CSV的Excel支持GBK编码，一定要转换，否则乱码
          $head [$i] = iconv ( 'utf-8', 'gbk', $v );
        }
        // 将数据通过fputcsv写到文件句柄
        fputcsv ( $fp, $head );

        foreach ($body as $key => $value) {
            foreach ($value as $k => $v) {
                $body[$key][$k] = iconv ( 'utf-8', 'gbk',$v);
            }
            fputcsv($fp, $body[$key]);
        }
        fclose($fp);
        return;
    }


    /**
     * [msg 返回数据]
     * @param  [type]  $code  [200：成功，203：非授权信息，400：错误请求，500：服务器错误]
     * @param  string  $msg   [description]
     * @param  [type]  $obj   [description]
     * @param  string  $url   [description]
     * @param  integer $count [description]
     * @return [type]         [description]
     */
    public function msg($code,$msg='',$obj=null,$url='',$count=0){
        $vars = array (
            'code' => $code,
            'msg' => $msg,
            'obj' => $obj,
            'url' => $url,
            'count' => $count,
        );
        return json_encode($vars);
    }

    /**
     * [msg 返回数据]
     * @param  [type]  $code  [200：成功，203：非授权信息，400：错误请求，500：服务器错误]
     * @param  string  $msg   [description]
     * @param  [type]  $obj   [description]
     * @param  string  $url   [description]
     * @param  integer $count [description]
     * @return [type]         [description]
     */
    public function msg2($code,$msg='',$obj=null,$url='',$count=0){
        $vars = array (
            'code' => $code,
            'msg' => $msg,
            'obj' => $obj,
            'url' => $url,
            'count' => $count,
        );
        return $vars;
    }

    /**
     * [msg 返回数据]
     * @param  [type]  $code  [200：成功，203：非授权信息，400：错误请求，500：服务器错误]
     * @param  string  $msg   [description]
     * @param  [type]  $obj   [description]
     * @param  string  $url   [description]
     * @param  integer $count [description]
     * @return [type]         [description]
     */
    public function eventmsg($event,$url='',$count=0){
        $code = '';
        $msg = '';
        foreach ($event as $key => $value) {
            if (!empty($value['code']) && $value['code'] != 200) {
                $code = $value['code'];
                $msg = $msg.$value['msg'].' ';
            }
        }
        $code = empty($code) ? $event[0]['code'] : $code;
        $msg = empty($msg) ? $event[0]['msg'] : $msg;

        $vars = array (
            'code' => $code,
            'msg' => $msg,
            'obj' => $event[0]['obj'],
            'url' => $url,
            'count' => $count,
        );
        return json_encode($vars);
    }

    /**
     * [removeEmoji 过滤掉emoji表情]
     * @param  string $nickname  [需过滤的文字]
     * @return [type]            [description]
     */
    public function removeEmoji ($nickname) {
        // $nickname = "💗࿐成✨功✨"; // 可以为将要发送的微信消息，包含emoji表情unicode字符串，需要转为utf8二进制字符串
        $nickname = preg_replace("/\\\u[ed20][0-9a-f]{3}/", "", json_encode($nickname)); //过滤掉微信表情
        return json_decode($nickname);
    }

    /**
     * [sendWeixinTemplate 发送微信模板消息]
     * @param  integer $length [description]
     * @return [type]          [description]
     */
    public function sendWeixinTemplate ($templateid, $data, $openid, $url=null) {
        try{
            $notice = $this->app()->notice;
            if(empty($url)){
                $notice->uses($templateid)->andData($data)->andReceiver($openid)->send();
            }else{
                $notice->uses($templateid)->withUrl($url)->andData($data)->andReceiver($openid)->send();
            }
        }catch(\Exception $e){
            Log::debug($e);
        }
    }

    /**
     * [formatStr 字符串参数格式化]
     * @param  integer $length [description]
     * @return [type]          [description]
     */
    public function formatStr ($txt='', $arr=[]) {
        // 精准替换
        // foreach ($arr as $key => $value) {
        //     $txt = preg_replace('/{{'.$key.'}}/', $value, $txt);
        // }
        // return $txt;

        // 模糊替换（仅限纯数字不包括小数点）
        // foreach ($arr as $key => $value) {
        //   $isTrue = preg_match('#{{'.$key.'_(\d*)}}#', $txt, $tempArr);
        //   if ($isTrue) {
        //     $key = $tempArr[0];
        //     $value = $tempArr[1] * $value;
        //     echo $key.'<br>';
        //     $txt = preg_replace('/'.$key.'/', $value, $txt);
        //   } else {
        //     $txt = preg_replace('/{{'.$key.'}}/', $value, $txt);
        //   }
        // }
        // return $txt;

        // 模糊替换（包括小数点）
        foreach ($arr as $key => $value) {;
          $txt = preg_replace('/{{'.$key.'}}/', $value, $txt);
          $isTrue = preg_match_all('#{{'.$key.'_([0-9]*.[0-9]*)}}#', $txt, $tempArr);
          if ($isTrue > 0) {
            $tempArr1 = $tempArr[0];
            $tempArr2 = $tempArr[1];
            for ($i=0; $i < count($tempArr1); $i++) {
                $key = $tempArr1[$i];
                $txt = preg_replace('/'.$key.'/', ($tempArr2[$i] * $value), $txt);
            }
          }
        }
        return $txt;
    }

    /**
     * [获取随机字符串nonceStr]
     * @param  integer $length [description]
     * @return [type]          [description]
     */
    public function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 导出CSV文件
     * @param array $data        数据
     * @param array $header_data 首行数据
     * @param string $file_name  文件名称
     * @return string
     */
    public function export_csv($header_data = [], $data = [], $file_name = '')
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$file_name.'.csv');
        header('Cache-Control: max-age=0');
        $fp = fopen('php://output', 'a');
        if (!empty($header_data)) {
            foreach ($header_data as $key => $value) {
                $header_data[$key] = iconv('utf-8', 'GB2312//IGNORE', $value);
            }
            fputcsv($fp, $header_data);
        }
        $num = 0;
        //每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 100000;
        //逐行取出数据，不浪费内存
        $count = count($data);
        if ($count > 0) {
            for ($i = 0; $i < $count; $i++) {
                $num++;
                //刷新一下输出buffer，防止由于数据过多造成问题
                if ($limit == $num) {
                    ob_flush();
                    flush();
                    $num = 0;
                }
                $row = $data[$i];
                foreach ($row as $key => $value) {
                    $row[$key] = iconv('utf-8', 'GB2312//IGNORE', $value."\t");
                }
                fputcsv($fp, $row);
            }
        }
        fclose($fp);
    }

    /**
     * [获取日期对应的周几]
     * @param  integer $length [description]
     * @return [type]          [description]
     */
    public function cmf_get_weekstr($week = 0) {
        $week_arr = array('1'=>'一','2'=>'二','3'=>'三','4'=>'四','5'=>'五','6'=>'六','7'=>'日');
        return $week_arr[$week];
    }

    /**
     * [获取两个时间内的周数]
     * @param  integer $length [description]
     * @return [type]          [description]
     */
    public function cmf_get_computeWeek($date1, $date2) {
        $diff = strtotime($date2) - strtotime($date1);
        $res = ceil($diff/(24*60*60*7));
        return $res;
    }

    /**
     * [获取两个时间内的所有日期]
     * @param  integer $length [description]
     * @return [type]          [description]
     */
    public function cmf_get_periodDate($start_time, $end_time) {
        $start_time = strtotime($start_time);
        $end_time   = strtotime($end_time);
        $i=0;
        while ($start_time<=$end_time){
            $arr[$i]=date('Y-m-d',$start_time);
            $start_time = strtotime('+1 day',$start_time);
            $i++;
        }
        return $arr;
    }

    /**
     * [导入excel内容转换成数组，import方法要用到]
     * @param  integer $length [description]
     * @return [type]          [description]
     */
    public function importExcelModel($filePath, $rg_row=3){

        $PHPExcel = new PHPExcel();//实例化，一定要注意命名空间的问题加\

        $PHPReader = new PHPExcel_Reader_Excel2007(); /**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/

        if(!$PHPReader->canRead($filePath)){
            $PHPReader = new PHPExcel_Reader_Excel5();
            if(!$PHPReader->canRead($filePath)){
                echo 'no Excel';
                return;
            }
        }

        $PHPExcel       = $PHPReader->load($filePath);
        $currentSheet   = $PHPExcel->getSheet(0);  //读取excel文件中的第一个工作表
        $allColumn      = $currentSheet->getHighestColumn(); //取得最大的列号
        $allRow         = $currentSheet->getHighestRow(); //取得一共有多少行
        $erp_orders_id  = array();  //声明数组

        /**从第二行开始输出，因为excel表中第一行为列名*/
        for($currentRow = $rg_row;$currentRow <= $allRow;$currentRow++){

            /**从第A列开始输出*/
            for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
                //这部分注释不要，取出的数据不便于我们处理
                // $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/
                //  if($val!=''){
                //  $erp_orders_id[] = $val;
                //  }
                //数据坐标
                $address = $currentColumn . $currentRow;
                //读取到的数据，保存到数组$arr中
                $data[$currentRow][$currentColumn] = explode("\n", $currentSheet->getCell($address)->getValue());
                /**如果输出汉字有乱码，则需将输出内容用iconv函数进行编码转换，如下将gb2312编码转为utf-8编码输出*/
                //echo iconv('utf-8','gb2312', $val)."\t";

            }
        }
        return $data;
    }
}