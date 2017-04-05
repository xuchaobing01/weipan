<?php
namespace User\Controller;
class MallOrderController extends UserController{
	public function export(){
		Vendor('PHPExcel.PHPExcel');
		$title = 'sale_data_'.date('YmdHis');
		$data[] = ['订单编号&状态','订单金额','收货人&电话','产品单价','订购数量','订单金额','收货地址','订单状态','下单时间'];
		
		createExcel($title,$data);
	}
}
?>