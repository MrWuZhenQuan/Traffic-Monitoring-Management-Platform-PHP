#### 编码注意事项

***

####命名规范

1. 文件命名

>
* 类文件都是以.class.php为后缀（这里是指的ThinkPHP内部使用的类库文件，不代表外部加载的类库文件），使用驼峰法命名，并且首字母大写，例如DbMysql.class.php；
* 确保文件的命名和调用大小写一致，是由于在类Unix系统上面，对大小写是敏感的（而ThinkPHP在调试模式下面，即使在Windows平台也会严格检查大小写）；

2. 变量命名

>
* 常量以大写字母和下划线命名，例如 HAS_ONE和 MANY_TO_MANY；
* 配置参数以大写字母和下划线命名，例如HTML_CACHE_ON；
* 其余变量使用驼峰式命名即可，见名知意

3. 函数和方法命名

>
* 方法的命名使用驼峰法，并且首字母小写或者使用下划线“_”，例如 getUserName，_parseType，通常下划线开头的方法属于私有方法；
* php函数（也即全局方法）的命名使用小写字母和下划线的方式，例如 get_client_ip
* 属性的命名使用驼峰法，并且首字母小写或者使用下划线“_”，例如 tableName、_instance，通常下划线开头的属性属于私有属性；

4. 数据库表和字段命名

#### 项目结构

***

	--okhaolvxing
	  |
	  --Backstage 后台代码文件夹
	  	|
	  	--Common 后台对应的配置文件，和外层相同，作用域不一样
	  	--Home 程序分层目录
	  	  |
	  	  -- Controller 控制器，此处代码处理页面跳转，权限控制
	  	  -- Model 模型，orm，操纵和配置数据库表和字段
	  	  -- Service 业务逻辑层，负责需求的业务逻辑计算
	  	  -- View 视图，与Controller下的控制器一一对应
	  	--Runtime 运行时产生的文件
	  --Common 保存公共配置文件,具体配置可在thinkphp帮助文档查找
	    --Conf
	      --db.config.php 保存数据库配置信息
	  --Public
	    |
	    -- backstage 后台视图对应的css，js资源
	      |
	      -- assert 后台页面用到的插件的css，js
	      -- css 自己写的后台页面用到的css
	      -- js 自己写的后台页面用到的js
	      -- framework 界面框架用到的css，js等东西
	  --ThinkPHP 框架文件夹
	  --.htaccess url重写文件
	  --index.php 入口文件
	  --README.md markdown文档	
	

#### 导入html框架

新建一个html文件，赋值下面代码，只需要在div中加入自己的页面代码即可

***

	<!DOCTYPE html>
	<html lang="en">
	<head>
	<title>菜单管理</title>
	</head>
	<body class="modern-ui">
	<include file="./Backstage/Home/View/Common/framework.html"/>
	<div class="span9" id="content">
			<!--在这里加入页面的代码即可-->
		</div>
	</body>
	</html>
