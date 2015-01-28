<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
  <head>
    <title>用户登陆</title>
    <!-- Bootstrap -->
    <link href="/Traffic-Monitoring-Management-Platform-PHP/Public/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="/Traffic-Monitoring-Management-Platform-PHP/Public/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
    <link href="/Traffic-Monitoring-Management-Platform-PHP/Public/assets/styles.css" rel="stylesheet" media="screen">
     <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="/Traffic-Monitoring-Management-Platform-PHP/Public/vendors/modernizr-2.6.2-respond-1.1.0.min.js"></script>
    <script src="/Traffic-Monitoring-Management-Platform-PHP/Public/vendors/jquery.cookie.js"></script>
  </head>
  <body id="login">
    <div class="container">

      <form class="form-signin" accept-charset="UTF-8" id="form-signin" action="<?php echo U('Index/index');?>" method="post" role="form">
        <h2 class="form-signin-heading">请登陆</h2>
        <input id="username" name="username" type="text" class="input-block-level" placeholder="LoginName">
        <input id="password" name="passwor" type="password" class="input-block-level" placeholder="Password">
        <label class="checkbox">
          <input id="rememberMe" type="checkbox" value="remember-me"> 记住密码
        </label>
        <div class="alert alert-error" id="tip" style="display:none"></div>
        <button id="btn_login" class="btn btn-large btn-primary" >登陆</button>
      </form>

    </div> <!-- /container -->
    <script src="/Traffic-Monitoring-Management-Platform-PHP/Public/vendors/jquery-1.9.1.min.js"></script>
    <script src="/Traffic-Monitoring-Management-Platform-PHP/Public/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function () {


        $("#btn_login").click(function (e) {
          // $('#tip').style.display ="";

                e.preventDefault();
            　　$.ajax({
            　　type:'post',//可选get
            　　url: "<?php echo U('Login/doLogin');?>",//这里是接收数据的PHP程序
            　　data:'username=' + $('#username').val() + '&password=' + $('#password').val(),//传给PHP的数据，多个参数用&连接
            　　dataType:'json',//服务器返回的数据类型 可选XML ,Json jsonp script html text等
            　　success:function (data) {
            　　//这里是ajax提交成功后，PHP程序返回的数据处理函数。msg是返回的数据，数据类型在dataType参数里定义!
                if (data.status == 0) {
                    $('#tip').text(data.content);
                    if(data.ismobile==1){
                      $('#form-signin').attr('action',"<?php echo U('Order/vm');?>");
                    }
                    $('#form-signin').submit();
                }
                $('#tip').text(data.content);
            }
            ,error:function (data) {
            　　//ajax提交失败的处理函数!
                $('#tip').text('网络出现错误，请稍后再试！');
            }

        });
            document.getElementById("tip").style.display="";
        });
      })
    </script>
  </body>
</html>