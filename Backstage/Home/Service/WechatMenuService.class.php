<?php 
namespace Home\Service;

/**
 * Class WechatReplyService
 * @author qax
 * Created_time: 12月30日
 */
class WechatMenuService extends CommonService {

	public function addWechatMenu($data){
		$data['created_at'] = time();
		$data['user_id'] = $_SESSION['USER_ID'];

		return $this->add($data,"t_wx_menu");
	}

	public function updateWechatMenu($data){
		$data['updated_at'] = time();
		return $this->update($data,"t_wx_menu","更新成功！");
	}

	public function deleteWechatMenu($data){

		return $this->delete($data,"t_wx_menu");
	}
}
 ?>