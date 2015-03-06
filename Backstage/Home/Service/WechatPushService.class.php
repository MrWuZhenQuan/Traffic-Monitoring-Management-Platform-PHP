<?php 
namespace Home\Service;

/**
 * Class WechatPushService
 * @author qax
 * Created_time: 1月5日
 */
class WechatPushService extends CommonService {

	/* 数据库表 */
	const CONTENTDATABASE = 't_news_release';
	const MATERIALDATABASE = 't_wx_material';

	public function getContent($data){
		$Model = M(self::CONTENTDATABASE)->find();

		return $Model;
	}

	public function addMaterial($data){
		$data['user_id'] = $_SESSION['USER_ID'];
		return $this->add($data,self::MATERIALDATABASE);
	}
}
 ?>