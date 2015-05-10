<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends CommonController {

// const DATABASE = 'traffic_data';
// const DEVICEDATABASE = 'device';
	public function start(){
	    $this->display();
	}

    public function index(){
    	$Model = M('user');
    	$this->display();
    }

    public function initView(){

        $Service = D('Index','Service'); //打开业务服务层
        $data = $Service->getDiffentPeriodData();//获取不同时期客流量数据
        $deviceName = $Service->getDeviceName();//获取设备名称
        $allDeviceData = $Service->getDiffentDeviceData();//获取不同设备客流量信息
        // p($deviceName);
        $this->assign('data',$data);//将获取不同时期客流量数据抛出
        $this->assign('device',$deviceName);//将设备名称数据抛出
        $this->assign('allDeviceData',$allDeviceData);//将不同设备客流量信息

        $this->display();
    }

    public function updateTestTime()
    {
        for ($i=1; $i <=3 ; $i++) { 
            $data['id'] = $i;
            $data['created_at'] = date('Y-m-d H:i:s');
            M('traffic_data')->save($data);
        }
    }
}