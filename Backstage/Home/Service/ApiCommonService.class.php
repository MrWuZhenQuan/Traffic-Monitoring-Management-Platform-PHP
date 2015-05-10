<?php

/**
 * @author sun
 */
namespace Home\Service;

abstract class ApiCommonService
{
    public function getSuccessResult(){
        return $this->getBaseResult("000", "处理成功");
    }

    public function getMissParamResult(){
        return $this->getBaseResult("001", "消息格式错误");
    }

    public function getIllegalResult(){
        return $this->getBaseResult("002", "用户信息不合法");
    }

    public function getInvailidResult(){
        return $this->getBaseResult("003", "非法调用");
    }

    public function getTypeErrorResult(){
        return $this->getBaseResult("004", "类型不正确");
    }

    public function getUserNotExist(){
        return $this->getBaseResult("005","帐号不存在");
    }

    public function getPasswordError(){
        return $this->getBaseResult("006","密码错误");
    }

    public function getUserMutidefine(){
        return $this->getBaseResult("007","用户名重复");
    }

    public function getPasswordIllegal(){
        return $this->getBaseResult("008","密码格式不正确");
    }

    public function getUnknowResult(){
        return $this->getBaseResult("999", "异常错误");
    }

    public function getEmptyContent(){
        return $this->getBaseResult('1000',"内容为空");
    }

    public function getBaseResult($respCode, $respDesc){
        $resultData['respCode'] = $respCode;
        $resultData['respDesc'] = $respDesc;
        return $resultData;
    }



    /**
     * 验证用户密码是否正确
     * @param $loginname
     * @param $password
     * @return bool
     */
    public function verifyUser($loginname, $password)
    {
        $adminModel = M('t_sys_user');
        $where['Loginname'] = $loginname;
        $modelResult = $adminModel->where($where)->find();
        $result = false;

        if ($modelResult != NULL && $modelResult != false) {
            if (md5($modelResult['Salt'] . $password) == $modelResult['Password']) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * aes+base64加密
     * @param $input
     * @param $key
     * @return stringt
     */
    public function encrypt($input, $key) {
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $input = $this->pkcs5_pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        $data = base64_encode($data);

        return $data;
    }

    /*
     * 返回app每次加载的条目
     * @param $model    传入对象
     * @param $where    查询条件，若无则填null
     * @param $beginNo  页码
     * @param $returnCount  每页数据的条数
     * @return mixed
     */
    protected function formPageData($model,$where,$field,$beginNo,$returnCount){
        $count = $model->where($where)->count();

        if($beginNo === null && $returnCount === null)
            $list = $model->field($field)->where($where)->select();
        else
            $list = $model->field($field)->where($where)->order('is_top desc')->limit($beginNo . ',' . $returnCount)->select();

        $data['list'] = $list;
        $data['count'] = $count;
        return $data;
    }

    private function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    /**
     * 使Map数组中的photo地址为缩略图（即获取缩略图地址）
     * @param $data    传入map数组
     * @param $returnCount  每页条数
     * @param $size  缩略图前缀
     * @return mixed
     */
    public function getThumbnail($data,$size,$key){
        if($key == ""){$key="photo";}
        $returnCount = count($data);
        for($i = 0;$i < $returnCount;$i++){
            $image[$i] = explode('/',$data[$i][$key]);
            if($data[$i][$key] != ''||$data[$i][$key] != null){
                $data[$i][$key] = 'Uploads';
                $image[$i][count($image[$i])-1] = $size.$image[$i][count($image[$i])-1];
                for($j = 1;$j < count($image[$i]);$j++){
                    $data[$i][$key] = $data[$i][$key].'/'.$image[$i][$j];
                }
            }
        }
        return $data;
    }

    /**
     * 增加点击次数
     * @param $Id 传入文章id
     * @param $table 传入表名
     * @param $belong_id 传入所属商家id
     * @return mixed
     */
    public function addClickNumber($id,$table,$belong_id){
        $loginName = $_SESSION['USERNAME'];
        $date = date("Y-m-d H:i:s");
        $clickModel = M('T_click_number');
        $data['username'] = $loginName;
        $data['belong_id'] = $belong_id;
        $data['table'] = $table;
        $data['table_id'] = $id;
        $data['click_time'] = $date;
        $clickModel -> create($data);
        $clickModel -> add($data);
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

    /**
     * 更新数据库sid储存的字段
     */
    private function updateUserSid($loginname){
        $userModel = M('t_sys_user');
        $sessionId = session_id();
        $userModel->SessionID = $sessionId;
        $userModel->where(array('Loginname' => $loginname))->save();
    }

    /**
     * 用户是否登录
     */
    public function isLogin($loginname,$sessionId){
        $dbSid = $this->getUserSessionID($loginname);
        if($dbSid === $sessionId)
            return true;
        return false;
    }

    protected function preNormal($imgPath){
        return $this->getScalePicPath($imgPath,C('NORMAL'));
    }

    protected function preSmall($imgPath){
        return $this->getScalePicPath($imgPath,C('Small'));
    }

    protected function preSquare($imgPath){
        return $this->getScalePicPath($imgPath,C('Square'));
    }

    protected function getScalePicPath($imgPath,$pre){
        $position = strripos($imgPath,"/",0);
        $path = substr($imgPath,0,++$position);
        $imgName = substr($imgPath,++$position,strlen($imgPath));
        return $path.$pre.$imgName;
    }
}

