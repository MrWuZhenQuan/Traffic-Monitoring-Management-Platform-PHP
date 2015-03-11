<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends CommonController {

const DATABASE = 'traffic_data';
const DEVICEDATABASE = 'device';

    private $deviceMsg = '';

	public function start(){
	    $this->display();
	}

    public function index(){
    	$Model = M('user');
    	$this->display();
    }

    public function initView(){
        $Model = M(self::DATABASE);

        $data = $this->getDiffentPeriodData();
        $deviceName = $this->getDeviceName();
        // p($deviceName);
        $this->assign('data',$data);
        $this->assign('device',$deviceName);

        $this->display();
    }

    private function getDeviceName(){
        foreach ($this->deviceMsg as $key => $value) {
            $name['s'][$key] = $value['name'];
            $name['b'][$key] = ucfirst($value['name']);
        }
        return json_encode($name,true);
    }

    /*获取Device的信息*/
    private function getDevice(){
        $Model = M(self::DEVICEDATABASE);
        $this->deviceMsg = $Model->select();
        return $this->deviceMsg;
    }

    private function getDiffentPeriodData(){
        $device = $this->getDevice();
        //p($d = date('Y-m-d H:i:s',$data[0]['created_at']));
        $array = array();
        /*创建数据格式模型*/
        $array = $this->createArrayModle($device);
        // p($array);
        /*获取人流量信息*/
        foreach ($device as $key => $value) {
            $traffic[$value['name']] =  M(self::DATABASE)->where(array('device_id' => $value['id']))->select();
        }
        // p($traffic);
        /*人流量数据合并*/
        foreach ($traffic as $key => $value) {
            $deviceTraffic[$key] = $this->mergeDate($array,$value);
        }

        // p($deviceTraffic);
        /*人流量数据填充*/
        foreach ($deviceTraffic as $device => $value) {

            foreach ($value as $key => $val) {
                $array[$key][$device] = $val;
            }
            
        }
        // p($array);

        /*时间格式化*/
        foreach ($array as $key => $value) {
            $array[$key]['time'] = date('Y-m-d H:i:s',$value['time']);
        }

        return json_encode($array,true);
    }

    private function mergeDate($array,$traffic){
        $data = $traffic;
        foreach ($array as $key => $value) {
            $hour = date('H',$value['time']);
            for ($i=0; $i < count($data); $i++) { 
                $date = strtotime($data[$i]['created_at']);
                $trafficHour = date('H',$date);
                if($hour == $trafficHour)
                    $visiters[$hour] +=$data[$i]['visiters'];
            }
        }

        return $visiters;
    }

    private function createArrayModle($device){
        $arr = array();
        $time = time();
        $currentHour = date('H',$time);
        $minute = date('i',$time)*60;
        $second = date('s',$time);
        for ($i=0; $i < 24; $i++) {
            if($i <= $currentHour){
                $arrModel = array('time' => $time-(($currentHour-$i)*3600)-$minute-$second, 'visits' => 0);
            }else if($i > $currentHour && $i<24){
                $arrModel = array('time' => $time+(($i-$currentHour)*3600)-$minute-$second, 'visits' => 0);
            }
            foreach ($device as $key => $value) {
                $arrModel[$value['name']] = 0;
            }

            array_push($arr,$arrModel);
        }

        return $arr;
    }
}