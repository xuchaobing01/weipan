<?php
/**
 * class QueryAction 处理查询接口
 * @date 2014/01/23
 * @author yanqizheng
 * @copyright SparkTech
 */
namespace Open\Controller;

use Think\Controller;

class UserController extends Controller
{
    private $APPKEY = 'b87d6e0d2d3f54de24b9e79abcdbdac0';
    private $vendor = 'V1001';
    private function certificate()
    {
        $sign              = I('get.sign');
        $data['timestamp'] = I('get.timestamp');
        $data['openid']    = I('get.openid');
        $data['vendor']    = I('get.vendor');
        $checkSign         = $this->sign($data);
        return $checkSign == $sign;
    }

    private function sign($data)
    {
        $data['appkey'] = $this->APPKEY;
        ksort($data, SORT_STRING);
        $tmp = http_build_query($data);
        return md5($tmp);
    }

    /**
     *@method merchant 获取用户关注的商户信息
     */
    public function check()
    {
        $sign              = I('get.sign');
        $data['timestamp'] = I('get.timestamp');
        $data['openid']    = I('get.openid');
        $data['vendor']    = I('get.vendor');
        $out['errcode']    = 0;
        if (empty($data['openid']) || empty($data['vendor']) || empty($data['timestamp']) || empty($sign)) {
            $out['errcode'] = 4000;
            $out['errmsg']  = 'invalid parameter!';
            exit(json_encode($out));
        }
        if (!is_valid_openid($data['openid'])) {
            $out['errcode'] = 4001;
            $out['errmsg']  = 'invalid openid!';
            exit(json_encode($out));
        }
        if ($this->vendor != $data['vendor']) {
            $this->responseJson(4004, 'invalid vendor!');
        }
        $checkSign = $this->sign($data);
        if ($checkSign !== $sign) {
            $out['errcode'] = 4005;
            $out['errmsg']  = 'sign faild!';
            exit(json_encode($out));
        }
        $ret = M('wechat_user')->where(['open_id' => $data['openid']])->field('token')->find();
        if ($ret == null) {
            $this->responseJson(2000, 'not focus!');
        }
        $merchant = M('wxuser')->where(['token' => $ret['token']])->field('wxname as wx_nickname,weixin as wx_name,wxid as wx_openid,appid,appsecret')->find();
        if ($merchant == null) {
            $this->responseJson(2000, 'not focus!');
        }
        $this->responseJson(0, '', $merchant);
    }

    private function responseJson($errcode, $errmsg = '', $data = [])
    {
        $data['errcode'] = $errcode;
        $data['errmsg']  = $errmsg;
        exit(json_encode($data));
    }

    public function getWxShareConfig()
    {
        $uid    = I('get.uid', 0, 'intval');
        $config = M('wxuser')->where(['uid' => $uid])->find();
        if ($config == null) {
            $this->responseJson(500, '用户不存在！');
        }
        $util                 = new \Spark\Wechat\WechatUtil($config['appid'], $config['appsecret']);
        $data['jsapi_ticket'] = $util->getJsApiTicket();
        $data['timestamp']    = time();
        $data['noncestr']     = create_key();
        $data['url']          = urldecode(I('get.ref', '', 'trim'));
        $plain                = 'jsapi_ticket=' . $data['jsapi_ticket'] . '&noncestr=' . $data['noncestr'] . '&timestamp=' . $data['timestamp'] . '&url=' . $data['url'];
        $signature            = sha1($plain);
        $this->responseJson(200, '', ['appid' => $config['appid'], 'signature' => $signature, 'timestamp' => $data['timestamp'], 'noncestr' => $data['noncestr']]);
    }
}
