<?php


if (! function_exists('ajax_result')) {
	/**
	 * 返回json数据
	 *
     * @param  [type]  $code  [200：成功，500：请求失败，403:未登录]
	 * @return string
	 */
	function ajax_result ($code, $msg='', $obj=null) {
        $data = array (
            'code' => $code,
            'msg' => $msg,
            'obj' => $obj
        );
        return json_encode($data);
	}
}
