<?php
$dbConfig = include('Common/Conf/db_config.php');
$appConfig =  array(
    // 调试页
    'SHOW_PAGE_TRACE' =>false,

    // 默认模块和Action
    'MODULE_ALLOW_LIST' => array('Home','Wap'),
    'DEFAULT_MODULE' => 'Home',
    //设置session的过期时间,以及session生效的域名
    'SESSION_OPTIONS' => array(
        'expire' => 60
    ),
    // 默认控制器
    'DEFAULT_CONTROLLER' => 'Index',
    //加盐的长度
    'SALT_LENGTH' => 6,
    //域名常量,添加菜单的时候默认进行填充
    'DOMAIN' => '/okhaolvxing/index.php/',
    'DOMAINIP' => 'http://192.168.1.100',
    //每页显示的条数
    'LIMITITEM' => 10,
    //导航一二级菜单名称
    'PARENTNAME' => 'PARENTNAME',
    'CHILDNAME' => 'CHILDNAME',
    //用户类型key
    'SUPER' => 'SUPER',
    'ADMIN' => 'ADMIN',
    'STORE' => 'STORE',
    'USER' => 'USER',
    'APPUSER' => 'APPUSER',
    //行业，职业，爱好
    'INDUSTRY' => 'INDUSTRY',
    'JOB' => 'JOB',
    'HOBBY' => 'HOBBY',
    //商品类别、优惠价
    'PRODUCT_TYPE' => 'PRODUCT_TYPE',
    'FAVOURABLE' => 'FAVOURABLE',
    'SYSTEM_TYPE' => 'SYSTEM_TYPE',
    //攻略
    'STRATEGY' => 'STRATEGY',
    //区域的类型
    'COUNTRY' => 0,
    'PROVINCE' => 1,
    'LAND' => 2,
    'DISTRICT' => 3,
    'VILLAGE' => 4,
     'TOWNS' => 5,
    //缩略图size
    'SMALL' => 'Small-',
    'NORMAL' => 'Normal-',
    'SQUARE' => 'Square-',
    //专题类别
    'TOPIC' => 'TOPIC',
    //当前页数
    'PAGE' => 'PAGE',
    
    //订单号-长度
    'ORDER_SN_LENGHT' => 7,
    //OK券号-长度
    'OK_SN_LENGHT' => 12,
    //优惠券号-长度
    'COUPON_SN_LENGHT' => 6,
);
//默认跳转链接
$defaultUrl = array(

);
$loginParams = array(
    'USERNAME' => 'USERNAME',
	'LOGINNAME' => 'LOGINNAME',
    'USER_ID' => 'USER_ID',
    'TYPE' => 'TYPE',
    'SUPERORNOT' => 'SUPERORNOT',
    'MENUS' => 'MENUS',
    'LOGID' => 'LOGID',
    'SESSIONID' => 'SESSIONID',
    'SITE_ID' => 'SITE_ID',
    'SITE_TYPE' => 'SITE_TYPE',
    'SITE_NAME' => 'SITE_NAME'
);

$storeTypeMap = array(
    'HOTEL' => 5,
    'SCENIC' => 6,
    'TRAVEL' => 4,
    'MUTI' => 7,
    'CINEMA' => 8
);

$productTypeMap = array(
    //电影通票
    'CINEMA_TICKET' => 57,
    //放映的电影
    'SHOW_MOVIE' => 56
);

$menuTypeMap = array(
    'PRODUCT_MANAGER' => 0,
    'MICROWEBSITE' => 1,
    'ALBUM' => 2,
    'WECHAT' => 3,
    'FINANCE_REPORTFORMS' => 4,
    'USER_FEEDBACK' => 5
);
//等待支付、成功、失败、取消
$orderStatus = array(
		'PAY_PENDING' => 0,
		'PAY_SUCCEED' => 1,
		'PAY_FAILURE' => 2,
		'PAY_CANCEL' => 3		
);

$LBS = array(
    'LBS_AK' => '4cc3da61554b94a51a171fa230bbd4cd',
    'LBS_SK' => '15d038ccb3ad948c0a3aa54370e5dd09',
    'GEO_TABLE' => 91984
);

return array_merge($orderStatus,$dbConfig,$productTypeMap,$appConfig,$defaultUrl,$loginParams,$storeTypeMap,$menuTypeMap,$LBS);
