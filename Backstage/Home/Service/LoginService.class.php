<?php
namespace Home\Service;

/**
 * Class LoginService
 * @package Home\Controller
 * @author edvard
 * @time 2014.9.23 7:26
 */
class LoginService extends CommonService
{

    /**
     * 查询该用户是否存在
     * @param $loginname
     * @param $password
     * @param $type
     * @return mixed
     */
    public function verify($loginname, $password,$type)
    {
            $adminModel = M('user');
            $where['loginname'] = $loginname;
            if($type != NULL)
                $where['Type'] = $type;

            $result = $adminModel->where($where)->find();
            if ($result != NULL && $result != false) {
                if (md5($result['salt'] . $password) == $result['password']) {
                    $data['mobile'] = $result['mobile'];
                    $data['status'] = 0;
                    $data['content'] = "登录成功";

                    session(C('USER_ID'),  $result['id']);
                    $this->updateUserSid($loginname);
                } else {
                    $data['status'] = 1;
                    $data['content'] = "密码错误";
                }
            } else {
                $data['status'] = 2;
                $data['content'] = "该用户不存在";
            }
            $superOrNot=$result['Type'] === 'SUPERADMIN'?true:false;
            $this->formReturnData($data,$result,$superOrNot);
            return $data;
    }

    /**
     * 合成返回的数据，包括状态
     * 和保存在session里面的数据
     * @param $data
     * @param $type
     */
    public function formReturnData($data,$result, $superOrNot)
    {
        /**
         * 如果登录成功，需要保存值到session
         */
        if ($data['status'] == 0) {
            session(C('USERNAME'),  $result['username']);
            session(C('LOGINNAME'), $result['loginname']);
            session(C('USER_ID'), $result['id']);
            session(C('TYPE'), $result['Type']);
            session(C('SUPERORNOT'), $superOrNot);
            //如果商家是多子账户，则需要找出对应的 Belong_to_which_site
            // session(C('SITE_ID'),$result['Belong_to_which_site']);

            // $site = M('t_sites')->field('name,type_name')->find($result['Belong_to_which_site']);
            // session(C('SITE_TYPE'), $site['type_name']);
            // session(C('SITE_NAME'), $site['name']);
        }
        return $data;
    }

    /**
     * 退出登录，暂时直接赋值为空
     * 后期输出信息
     */
    public function exitLogin()
    {
        if(session(C('USER_ID')) == null){
          // if (session(C('LOGINNAME')) == null || 
        		// session(C('USER_ID')) == null ||
            	// session(C('TYPE')) == null ||
           		// session(C('SUPERORNOT') == null)
        // )
            return $this->errorResultReturn("您还未登录");
        }else {
            session(C('USERNAME'), null);
            session(C('LOGINNAME'), null);
            session(C('USER_ID'), null);
            session(C('TYPE'), null);
            session(C('SUPERORNOT'), null);
            // $this->exitLoginLog();
            return $this->successResultReturn("成功退出登录");
        }
    }

    /**
     * 往登录日志插入一条登录记录
     * 并且插入session
     */
    public function loginLog($username){
        $model = M('t_sys_login_log');
        $user['Loginname'] = session(C('LOGINNAME'));
        $user['UserType'] = session(C('TYPE'));
        //暂时使用pc来填充此字段
        $user['WhichSource'] = 'PC';
        $user['Login_ip'] = get_client_ip();
        $user['Login_at'] = date("Y-m-d H:i:s");

        import('ORG.Net.IpLocation');
        $Ip=new \Org\Net\IpLocation("UTFWry.dat");//导入ip库 参考http://thinkphp.cn/extend/270.html  把下载的文件放在Lib\ORG\Net下
        $area=$Ip->getlocation(get_client_ip());
        $user['LoginCity'] = $area['area'];

        $model->create($user);
        $id = $model->add($user);
        session(C('LOGID'),$id);
    }

    /**
     * 更新该用户的退出登录记录
     */
    public function exitLoginLog(){
        $model = M('t_sys_login_log');
        $model->where(array('ID' => session(C('LOGID'))))->setField('Exit_at',date("Y-m-d H:i:s"));
    }

}
