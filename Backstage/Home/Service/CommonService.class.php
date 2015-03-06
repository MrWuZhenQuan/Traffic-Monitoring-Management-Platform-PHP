<?php

/**
 * @author edvard
 */
namespace Home\Service;


abstract class CommonService
{

    /**
     * @param $data 数据
     * @param $db 表名
     * @param $info 成功信息
     * @return array
     */
    public function add($data,$db,$info){

        if(empty($info)) $info = "添加成功！";
        $Service = M($db);
        if(false === ($service = $Service->create($data))){
            return $this->errorResultReturn($Service->getError());
        }
        if(false === $Service->add($data)){
            return $this->errorResultReturn('系统出错了！');
        }

        $lastId = $Service->id ? $Service->id : $Service->getLastInsId();

        return $this->successResultReturn($info, $lastId);
    }

    /**
     * 数据库删除操作
     * 返回结果值
     * @param  int $id
     * @param  string $db
     * @return array
     */
    protected function delete($id,$db,$info){
        
        if(!empty($id)&&isset($id)){
            
            if(empty($info)) $info = "删除成功！";
            $Service = M($db);
            if(false === $Service->delete($id)){

                return $this->errorResultReturn('系统出错了！');
            }
            return $this->successResultReturn($info);
        }
        return $this->errorResultReturn('无效操作！');
    }

    /**
     * 批量数据库删除操作
     * 返回结果值
     * @param  array $id
     * @param  string $db
     * @return array
     */
    public function deletes($id,$db,$info){
        if(!empty($id)&&isset($id)){
            if(empty($info)) $info = "删除成功！";
            $Service = M($db);
            foreach ($id as $key) {
                if(false === $Service->delete($key)){
                    return $this->errorResultReturn('系统出错了！');
                }
            }
            return $this->successResultReturn($info);
        }
        return $this->errorResultReturn('无效操作！');      
    }

    /**
     *
     * @param $data 数据
     * @param $db 表名
     * @param $info 成功信息
     * @return array
     */
    public function update($data,$db,$info){


        if((!empty($data['id'])&& isset($data['id']))||(!empty($data['ID'])&& isset($data['ID']))){

            if(empty($info)) $info = "编辑成功！";
            $Service = M($db);

            if(false === ($service = $Service->create($data))){

                return $this->errorResultReturn($Service->getError());
            }
            if(false === $Service->save($data)){
                return $this->errorResultReturn($Service->getError());
            }
            return $this->successResultReturn($info);
        }
        return $this->errorResultReturn("无效操作！");
    }


    /**
     * 数据库是否隐藏操作
     * 返回结果值
     * @param  int $id 数据id
     * @param  string $db 数据库
     * @param  string $temp 变更字段名
     * @return array
     */
    public function is_show($id,$db,$temp)
    {
        $Model = M($db);
        $show = $Model->find($id);
        $data = $show;
        if ($show[$temp] === '0') {
            $data[$temp] = '1';
        } else {
            $data[$temp] = '0';
        }
        $info = $this->update($data,$db,'操作成功');
        return $info;
    }

    /**
     * 返回结果值
     * @param  int $status
     * @param  fixed $data
     * @return array
     */
    protected function resultReturn($status, $data)
    {
        return array('status' => $status,
            'data' => $data);
    }

    /**
     * 返回错误的结果值
     * @param  string $success 错误信息
     * @return array         带'success'键值的数组
     */
    protected function successResultReturn($info, $extras)
    {
        return $this->resultReturn(true, array('success' => $info, 'extras' => $extras));
    }

    /**
     * 返回错误的结果值
     * @param  string $error 错误信息
     * @return array         带'error'键值的数组
     */
    protected function errorResultReturn($error)
    {
        return $this->resultReturn(false, array('error' => $error));
    }

    /**
     * 填充上刚插入数据的操作者信息
     * 对应的右边名称
     * 添加者 add_user
     * 添加者的类型 add_type
     * 添加者id add_id
     * 添加者ip add_ip
     * 添加时间 add_at
     */
    public function appendInsertOptInfo($data)
    {
        return $this->appendArray(array('add_user','add_type','add_id','add_ip','add_at'),$data);
    }

    /**
     * 填充上修改或者删除的操作者信息
     * 最后操作者 last_user
     * 操作者的类型 last_type
     * 最后操作者id last_id
     * 最后操作者ip last_ip
     * 最后添加时间 last_at
     */
    public function appendLastOptInfo($data)
    {
        return $this->appendArray(array('last_user','add_type','last_id','last_ip','last_at'),$data);
    }

    /**
     * 填充的基类
     * @param $keys
     * @param $data
     * @return mixed
     */
    private function appendArray($keys, $data)
    {
        $data[$keys[0]] = session(C('USERNAME'));
        $data[$keys[1]] = session(C('TYPE'));
        $data[$keys[2]] = session(C('USER_ID'));
        $data[$keys[3]] = get_client_ip();
        $data[$keys[4]] = date("Y-m-d H:i:s");
        return $data;
    }

    /**
     * 列表的编辑和删除的url拼接方法
     * @param $list
     * @return mixed
     */
    protected function appendOpt($list,$nameModify,$nameDel){
        for($i = 0;$i < count($list); $i ++){
            $list[$i]['modify_url'] = U(CONTROLLER_NAME.'/'.$nameModify,array('id' => $list[$i]['ID']));
            $list[$i]['del_url'] = U(CONTROLLER_NAME.'/'.$nameDel,array('id' => $list[$i]['ID']));
        }
        return $list;
    }

    /**
     * 返回分页的数据
     * @param $model
     * @param $where
     * @param $modifyName
     * @param $delName
     * @return mixed
     */
    protected function formPageData($model,$where,$modifyName,$delName){
        $count = $model->where($where)->count();
        $page = new \Think\Page($count, C('LIMITITEM'));
        $show = $page->show();

        $list = $model->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
        $list = $this->appendOpt($list, $modifyName, $delName);

        $data['list'] = $list;
        $data['page'] = $show;
        $data['js'] = $page->js();

        return $data;
    }

    protected function formOrderPageData($model,$where,$modifyName,$delName,$orderFilter){
        $count = $model->where($where)->count();
        $page = new \Think\Page($count, C('LIMITITEM'));
        $show = $page->show();

        $list = $model->where($where)->order($orderFilter)->limit($page->firstRow . ',' . $page->listRows)->select();
        $list = $this->appendOpt($list, $modifyName, $delName);

        $data['list'] = $list;
        $data['page'] = $show;
        $data['js'] = $page->js();

        return $data;
    }

    /**
     *根据id获取用户所有信息
     * @param $Id 传入sessionid
     * @return mixed
     */
     public function getUserInfo($Id){
         $model = M('T_sys_user');
         $info = $model -> find($Id);
         return $info;
     }
     /**
      * @param $msg 内容
      * @param $filename 文件名
      */
     function writeLog($msg,$filename="syslog.txt"){
     	$filename='log/'.date('Ymd').'_'.$filename;
     	file_put_contents($filename,"Time ".date('Y-m-d H:i:s').$msg."\r\n",FILE_APPEND);
     }

    /**
     * 更新数据库sid储存的字段
     */
    public function updateUserSid($loginname){
        $userModel = M('user');
        $sessionId = session_id();
        $userModel->SessionID = $sessionId;
        $userModel->where(array('loginname' => $loginname))->save();
    }

    /**
     * 设置一个登录帐号的sid值
     * @param $loginnanme 需要设置的用户
     * @return string 返回设置后的sessionid值
     */
    public function setUserSessionID($loginnanme){
        $userModel = M('t_sys_user');
        session_start();
        $sessionID = session_id();
        $userModel->SessionID = $sessionID;
        $userModel->where(array('Loginname' => $loginnanme))->save();
        return $sessionID;
    }

    /**
     * 得到指定用户的sessionid
     * @param $loginname 指定用户
     * @return mixed 储存在数据库中的sessionid
     */
    public function getUserSessionID($loginname){
        $userModel = M('t_sys_user');
        $userSid = $userModel->where(array('Loginname' => $loginname))->field('SessionID')->find();
        return $userSid['SessionID'];
    }

}

