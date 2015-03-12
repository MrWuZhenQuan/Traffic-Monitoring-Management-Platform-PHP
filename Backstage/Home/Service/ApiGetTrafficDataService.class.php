<?php
namespace Home\Service;

/**
 * Class ApiGetTrafficDataService
 * @package Home\Controller
 * @author qax
 * @time 2014.10.14 7:26
 */
class ApiGetTrafficDataService extends ApiCommonService
{
	public function getType(){
		return $this->getSuccessResult();
	}
	
	public function error(){
		return $this->getInvailidResult();
	}

	public function add($data){

        $Service = M("traffic_data");
        $data['created_at'] = date('Y-m-d H:i:s');
        if(false === ($service = $Service->create($data))){
            return $this->getMissParamResult();
        }

        if(false === $Service->add($data)){

            return $this->getUnknowResult();
        }

        return $this->getSuccessResult();
    }
}