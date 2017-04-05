<?php
namespace Shop\Logic;
class Cart{
	public function __construct() {
		if(session('cart') == false){
			session('cart',array());
		}
		$this->cart = session('cart');
	}
	
	/*
	添加商品
	int $id 商品主键
	string $name 商品名称
	float $price 商品价格
	int $num 购物数量
	string $spec
	*/
	public function addItem($id,$item,$num,$spec) {
		if ($this->cart[$id] != false) {
			return 1;
		}
		$item['num'] = $num;
		$item['spec'] = $spec;
		$this->cart[$id] = $item;
		session('cart',$this->cart);
	}

	/*
	修改购物车中的商品数量
	int $id 商品主键
	int $num 某商品修改后的数量，即直接把某商品
	的数量改为$num
	*/
	public function modNum($id,$num=1) {
		if (!isset($this->cart[$id])) {
			return false;
		}
		$this->cart[$id]['num'] = $num;
		session('cart',$this->cart);
	}

	/*
	商品数量+1
	*/
	public function incNum($id,$num=1) {
		if (isset($this->cart[$id])) {
			$this->cart[$id]['num'] += $num;
		}
		session('cart',$this->cart);
	}

	/*
	商品数量-1
	*/
	public function decNum($id,$num=1) {
		if (isset($this->cart[$id])) {
			$this->cart[$id]['num'] -= $num;
		}
		//如果减少后，数量为0，则把这个商品删掉
		if ($this->cart[$id]['num'] <1) {
			$this->delItem($id);
		}
		session('cart',$this->cart);
	}

	/*
	删除商品
	*/
	public function delItem($id) {
		unset($this->cart[$id]);
		session('cart',$this->cart);
	}
	
	/*
	获取单个商品
	*/
	public function getItem($id) {
		return $this->cart[$id];
	}
	
	/*
	获取所有商品
	*/
	public function getItems() {
		return $this->cart;
	}
	
	/*
	查询购物车中商品的种类
	*/
	public function getCnt() {
		return count($this->cart);
	}
	
	/*
	查询购物车中商品的个数
	*/
	public function getNum(){
		if ($this->getCnt() == 0) {
			return 0;
		}
		$sum = 0;
		foreach ($this->cart as $item) {
			$sum += $item['num'];
		}
		return $sum;
	}

	/*
	购物车中商品的总金额
	*/
	public function getPrice() {
		//数量为0，价钱为0
		if ($this->getCnt() == 0) {
			return 0;
		}
		$price = 0.00;
		foreach ($this->cart as $item) {
			$price += $item['num'] * $item['oprice'];
		}
		return sprintf("%01.2f", $price);
	}

	/*
	清空购物车
	*/
	public function clear() {
		$this->cart = array();
		session('cart',array());
	}
}