<?php
	$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
	wl_load()->model('setting');
	load()->func('communication');
	if($op == 'display'){
		$domain = $_W['siteroot']; 
		$siteid = $_W['setting']['site']['key'];
		//$resp = ihttp_request('http://weixin.weliam.cn/addons/weliam_manage/api.php', array('type' => 'get_task','module' => 'feng_fightgroups','uniacid'=>$_W['uniacid']),null,1);
		$resp = @json_decode($resp['content'], true);
		$task_status = $resp['result'];
		if(checksubmit('submit')){
			$task_status = !empty($_GPC['task_status'])?1:2;
			$request = app_url("home/auto_task");
			//$resp = ihttp_request('http://weixin.weliam.cn/addons/weliam_manage/api.php', array('type' => 'task','module' => 'feng_fightgroups','website' => $siteid,'status'=>$task_status,'request'=>$request,'uniacid'=>$_W['uniacid']),null,1);
			$resp = @json_decode($resp['content'], true);
			if($resp['result']==1){
				message("计划任务更改成功",web_url("application/task/display"),'success');
			}else{
				message("计划任务更改成功",web_url("application/task/display"),'success');
			}
		}
	}
	if($op == 'list'){
		$message['status']=tgsetting_read('task');
		if(checksubmit('submit')){
			$data = $_GPC['m_status'];
			tgsetting_save($data,'task');
			message("保存成功",web_url("application/task/list"),'success');
		}
		
	}
	include wl_template('system/task');