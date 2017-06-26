<?php
	$ops = array('display');
	$op_names = array('商品统计');
	foreach($ops as$key=>$value){
		permissions('do', 'ac', 'op', 'data', 'goods_data', $ops[$key], '数据中心', '商品统计', $op_names[$key]);
	}
	$op = in_array($op, $ops) ? $op : 'display';
	if($op=='display'){
		if($_GPC['type']=='salenum' || empty($_GPC['type'])){
			$where = array();
			if(TG_MERCHANTID)$where['merchantid'] = $_SESSION['role_id'];
			$where['#isshow#'] = '(1,2,3)';
			$dataData = model_goods::getNumGoods('*', $where, 'salenum desc', 0, 10, 1);
			$data['list'] = $dataData[0];
			foreach($data['list'] as$key=>&$value){
				$labels[] = cutstr($value['gname'], 7, true);
				$salenum[] = $value['salenum'];
			}
		}
		$dataData = model_goods::getNumGoods('*', $where, 'salenum desc', 0, 10, 1);
		$data1['list'] = $dataData[0];
		$dataData = model_goods::getNumGoods('*', $where, 'pv desc', 0, 10, 1);
		$data2['list'] = $dataData[0];
		$dataData = model_goods::getNumGoods('*', $where, 'uv desc', 0, 10, 1);
		$data3['list'] = $dataData[0];
		$dataData = model_goods::getNumGoods('*', $where, 'uv desc', 0, 10, 1);
		$data4['list'] = $dataData[0];
		
		foreach($data1['list'] as$key=>&$value){
			$init = 0;
			$orderData = model_order::getNumOrder('*', array('g_id'=>$value['id'],'#status#'=>'(1,2,3,4)'), 'id desc', 0, 0, 0);
			foreach($orderData[0]?$orderData[0]:array() as $v){
				$init += $v['price'];
			}
			$value['money'] = $init;
		}
		
		foreach($data2['list'] as$key=>&$value){
			$init = 0;
			$orderData = model_order::getNumOrder('price', array('g_id'=>$value['id'],'#status#'=>'(1,2,3,4)'), 'id desc', 0, 0, 0);
			foreach($orderData[0]?$orderData[0]:array() as $v){
				$init += $v['price'];
			}
			$value['money'] = $init;
		}
		foreach($data3['list'] as$key=>&$value){
			$init = 0;
			$orderData = model_order::getNumOrder('price', array('g_id'=>$value['id'],'#status#'=>'(1,2,3,4)'), 'id desc', 0, 0, 0);
			foreach($orderData[0]?$orderData[0]:array() as $v){
				$init += $v['price'];
			}
			$value['money'] = $init;
		}
		foreach($data4['list'] as$key=>&$value){
			$init = 0;
			$orderData = model_order::getNumOrder('price', array('g_id'=>$value['id'],'#status#'=>'(1,2,3,4)'), 'id desc', 0, 0, 0);
			foreach($orderData[0]?$orderData[0]:array() as $v){
				$init += $v['price'];
			}
			$value['money'] = $init;
		}
		$money = $data4['list'];
		$flag=array();
		foreach($money as $arr2){
		    $flag[]=$arr2["money"];
		    }
		array_multisort($flag,SORT_DESC,$money);
		include wl_template('data/goods_data');
	}
	