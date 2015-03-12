<?php 
namespace Home\Service;

/**
 * Class WechatBindingService
 * @author qax
 * Created_time: 12月30日
 */
class IndexService extends CommonService {
	const DATABASE = 'traffic_data';
	const DEVICEDATABASE = 'device';

	private $deviceMsg = '';
	private $zeroTime ='';


	public function getDeviceName(){
        foreach ($this->deviceMsg as $key => $value) {
            $name['s'][$key] = $value['name'];
            $name['b'][$key] = ucfirst($value['name']);
        }
        return json_encode($name,true);
    }

    public function getDiffentDeviceData(){
		$traffic = $this->getTrafficData();
		$i=0;
		foreach ($traffic as $key => $value) {
			
			foreach ($value as $k => $v) {
				$mD[$i]['device'] = ucfirst($key); 
				$mD[$i]['traffic'] += $v['visiters']; 
			}
			$i += 1;
		}
		// p($mD);
		foreach ($mD as $k => $v) {
			$all +=$v['traffic'];
		}
		foreach ($mD as $k => $v) {
			$count = $v['traffic'] / $all;
			$m[$k]['label'] = ucfirst($v['device']);
			$m[$k]['value'] = floor(round($count,4)*100*100)/100;
			
		}
		/*数据合并*/
		$result['dount'] = $m;
		$result['bar'] = $mD;

    	return json_encode($result);
    }



    /*获取人流量信息*/
    public function getTrafficData($time){
    	$device = $this->getDevice();
        foreach ($device as $key => $value) {
            $traffic[$value['name']] =  M(self::DATABASE)->where(array('device_id' => $value['id'],'created_at'=>array('EGT',date('Y-m-d H:i:s',$time))))->select();
        }
        return $traffic;
    }

    public function getDiffentPeriodData(){
		/*获取人流量信息*/
    	$traffic = $this->getTrafficData($this->zeroTime);
        //p($d = date('Y-m-d H:i:s',$data[0]['created_at']));
        $array = array();
        /*创建数据格式模型*/
        $array = $this->createArrayModle($this->deviceMsg);

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

        /*获取Device的信息*/
    private function getDevice(){
        $Model = M(self::DEVICEDATABASE);
        $this->deviceMsg = $Model->select();
        return $this->deviceMsg;
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
        /*计算零点时间**/
        $this->zeroTime = $time-($currentHour*3600)-$minute-$second;
        for ($i=0; $i < 24; $i++) {
            if($i <= $currentHour){
                $arrModel = array('time' => $time-(($currentHour-$i)*3600)-$minute-$second);
            }else if($i > $currentHour && $i<24){
                $arrModel = array('time' => $time+(($i-$currentHour)*3600)-$minute-$second);
            }
            foreach ($device as $key => $value) {
                $arrModel[$value['name']] = 0;
            }

            array_push($arr,$arrModel);
        }

        return $arr;
    }

}