<?php
namespace Wap\Controller;
use Think\Controller;
class SeccodeController extends Controller {
     /* 生成验证码 */
    public function verify()
    {
        $config = [
            'fontSize' => 24, // 验证码字体大小
            'length' => 4, // 验证码位数
            'useNoise'    =>    false,
            'useCurve'=>false,
        ];
       $Verify = new \Think\Verify($config);
        $Verify->entry();
    }

    /* 验证码校验 */
	function check_verify($code, $id = ''){
	    $verify = new \Think\Verify();
	    return $verify->check($code, $id);
	}



}