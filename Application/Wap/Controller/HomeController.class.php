<?php
/**
 * @class WapController Wap模块控制器基类
 */
namespace Wap\Controller;
use Think\Controller;
class HomeController extends Controller {
    public function index(){
        $this->display();
    }
}
?>