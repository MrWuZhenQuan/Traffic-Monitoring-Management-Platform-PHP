<?php 
namespace Home\Service;

/**
 * Class WechatReplyService
 * @author qax
 * Created_time: 12月30日
 */
class WechatReplyService extends CommonService {

	public function addLinkReply($data){
		$data['link_trunk_items_count'] = count($data);
		$linkId = $this->addAndGetId($data,"wx_link_trunk");
	}

	public function addLinkTrunkItem($data){
		return $this->add($data,"wx_link_trunk_item");
	}

	public function addWechatUser($data){

		return $this->add($data,"wx_users");
	}

	public function updateWechatUser($data){

		return $this->update($data,"wx_users");
	}

	public function addFollowText($data){

		return $this->add($data,"wx_respond");
	}

	public function updateFollowText($data){

		return $this->update($data,"wx_respond","更新成功！");
	}

    /**
     * @param $data 数据
     * @param $db 表名
     * @param $info 成功信息
     * @return id
     */
    public function addAndGetId($data,$db,$info){

        if(empty($info)) $info = "添加成功！";
        $Service = M($db);

        if(false === ($service = $Service->create($data))){
            return $this->errorResultReturn($Service->getError());
        }

        if(false === $id = $Service->add($data)){
            
            return $this->errorResultReturn('系统出错了！');
        }

        return $id;
    }
}
 ?>