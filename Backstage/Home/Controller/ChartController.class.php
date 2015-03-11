<?php

namespace Home\Controller;
use Think\Controller;
class ChartController extends CommonController {

    const DATABASE = 'traffic_data';

    public function index(){
        $data = $this->getDiffentPeriodData($device_id);
        p($data);die;
        $data = M(self::DATABASE)->select();
        //p($d = date('Y-m-d H:i:s',$data[0]['created_at']));
        $array = array();
        $currentHour = date('H');
        for ($i= 1; $i <= $currentHour ; $i++) {
            $visiters[$i] = 0;
            for ($j= 0; $j < count($data) ; $j++) {
                $hour = date('H',$data[$j]['created_at']);
                if($i == $hour)
                    $visiters[$i] += $data[$j]['visiters'];
            }
            $chartData = array('period' => $i.'点', 'visits' => $visiters[$i],'device' => '');
            array_push($array,$chartData);
        }
        p($array);
        for ($i=0; $i <6 ; $i++) { 
           p($d = date('H',$data[$i]['created_at'])); 
        }
        die;
        $this->ajaxReturn($data);
    	$this->display();
    }

    private function getDiffentPeriodData($device_id){
        $data = M(self::DATABASE)->select();
        //p($d = date('Y-m-d H:i:s',$data[0]['created_at']));
        $array = array();
        $array = $this->createArrayModle();
        $currentHour = date('H');
        for ($i= 1; $i <= $currentHour ; $i++) {
            $visiters[$i] = 0;
            for ($j= 0; $j < count($data) ; $j++) {
                $hour = date('H',$data[$j]['created_at']);
                if($i == $hour)
                    $visiters[$i] += $data[$j]['visiters'];
            }
            $array[$i-1] = array('period' => $i.'点', 'visits' => $visiters[$i],'device' => '');
            // $chartData = array('period' => $i.'点', 'visits' => $visiters[$i],'device' => '');
            // array_push($array,$chartData);
        }

        return $array;
    }

    private function createArrayModle(){
        $arr = array();
        for ($i=1; $i <= 24; $i++) { 
            $arrModel = array('period' => $i.'点', 'visits' => '','device' => '');
            array_push($arr,$arrModel);
        }

        return $arr;
    }
}