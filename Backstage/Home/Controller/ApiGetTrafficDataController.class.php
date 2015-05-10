<?php

namespace Home\Controller;
use Think\Controller;

/**
*author:qax
*date:2015.03.09
*/

class ApiGetTrafficDataController extends Controller {

	public function trafficData(){
		//参数格式
		/*req={"visiters":123,"device_id":2}*/
		$get = json_decode($_GET['req'],true);

		$data['visiters'] = $get['visiters'];
		$data['device_id']= $get['device_id'];
		$Service = D('ApiGetTrafficData','Service');

		echo json_encode($Service->add($get));
	}
}