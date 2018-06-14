<!DOCTYPE html>
<html class="js cssanimations"><head lang="en">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta charset="UTF-8">
  <title>登录</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="format-detection" content="telephone=no">
  <meta name="renderer" content="webkit">
  <meta http-equiv="Cache-Control" content="no-siteapp">
  <link rel="alternate icon" type="image/png" href="files/Glutton.png">
  <link rel="stylesheet" href="http://cdn.amazeui.org/amazeui/2.7.2/css/amazeui.min.css">
</head>
	<script src="https://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
	<script src="http://cdn.amazeui.org/amazeui/2.7.2/js/amazeui.min.js"></script> 
	<script src="http://cdn.bootcss.com/blueimp-md5/1.1.0/js/md5.js"></script>
	<script src="./bin/jsencrypt.min.js"></script>
	<script src="./bin/encode.js"></script>
	
	<body>
		<header class="am-topbar">
		  <h1 class="am-topbar-brand">
			<a href="#">管理员登录</a>
		  </h1>
		</header>

		<div class="am-g">
		  <div class="am-u-md-8 am-u-sm-centered">
			<form class="am-form" method="post" action="log_in_handle.php">
			  <fieldset class="am-form-set">
				<input type="text" placeholder="用户" name="u_name" id="usrn">
				<input type="password" placeholder="密码" name="u_pwd" id="pswd">
			  </fieldset>
			  
			</form>
			<button class="am-btn am-btn-primary am-btn-block" id="submit">登录</button>
		  </div>
		</div>
	</body>
	<script>
		$(function(){
			$("#submit").click(function(){
				let usr = $("#usrn").val();
				let pwd = $("#pswd").val();
				let Md5Pwd = md5(pwd);
				
				if(usr == '' || pwd == '')
					alert("请输入完整信息！");
				else
				{
					let JSONobj = {
						"userName":	usr,
						"password":	Md5Pwd
					};
					
					let userInfo = JSON.stringify(JSONobj);
					userInfo = encode(userInfo);
					//alert(userInfo);
					
					
					 $.post("/PHP/user/login.php", userInfo ,
							function(data,status){
							
							let stat = data["loginStatus"];
							
							
							if(stat == 1)
								alert('Fail');
							
							window.location.href='index.php';
								
					});
				}
			});
			
		});
	</script>
</html>