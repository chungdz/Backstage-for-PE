<?php
	include '../com_fun.php';
	include '../../PHP/user/userManager.php';
	header("Content-type: text/html; charset=utf-8"); 
	check_log($_UserManager);
?>

<!DOCTYPE html>
<html class="js cssanimations"><head lang="en">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta charset="UTF-8">
  <title>增加新用户</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="format-detection" content="telephone=no">
  <meta name="renderer" content="webkit">
  <meta http-equiv="Cache-Control" content="no-siteapp">
  <link rel="alternate icon" type="image/png" href="files/Glutton.png">
  <link rel="stylesheet" href="http://cdn.amazeui.org/amazeui/2.7.2/css/amazeui.min.css">
  <style>
    @media only screen and (min-width: 1200px) {
      .blog-g-fixed {
        max-width: 1200px;
      }
    }

    @media only screen and (min-width: 641px) {
      .blog-sidebar {
        font-size: 1.4rem;
      }
    }

    .blog-main {
      padding: 20px 0;
    }

    .blog-title {
      margin: 10px 0 20px 0;
    }

    .blog-meta {
      font-size: 14px;
      margin: 10px 0 20px 0;
      color: #222;
    }

    .blog-meta a {
      color: #27ae60;
    }

    .blog-pagination a {
      font-size: 1.4rem;
    }

    .blog-team li {
      padding: 4px;
    }

    .blog-team img {
      margin-bottom: 0;
    }

    .blog-content img,
    .blog-team img {
      max-width: 100%;
      height: auto;
    }

    .blog-footer {
      padding: 10px 0;
      text-align: center;
    }
  </style>
  
<script src="https://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="http://cdn.amazeui.org/amazeui/2.7.2/js/amazeui.min.js"></script>  
</head>
<body>



	<header class="am-topbar">
	  <h1 class="am-topbar-brand">
		<a href="userinfo.php">添加用户</a>
	  </h1>

	  <button class="am-topbar-btn am-topbar-toggle am-btn am-btn-sm am-btn-success am-show-sm-only" data-am-collapse="{target: '#doc-topbar-collapse'}"><span class="am-sr-only">导航切换</span> <span class="am-icon-bars"></span></button>

	</header>

	<form class="am-form" enctype="multipart/form-data" name="form" data-am-validator>
	  <fieldset>
		
			<div class="am-form-group">
			  <label for="doc-ta-1">*用户名</label>
			  <textarea class="" rows="1" name="username" id="usrn" required/></textarea>
			</div>
			
			<div class="am-form-group">
			  <label for="doc-ta-1">*密码</label>
			  <textarea class="" rows="1" name="password" id="pswd" required/></textarea>
			</div>
			
			<div class="am-form-group">
			  <label for="doc-ta-1">*管理员权限</label>
			  <select name="isAdmin" id="power" required/>
			    <option value = "0">普通用户</option>
				<option value = "1">管理员</option>
				</select>
			</div>
			
			<div class="am-form-group">
			  <label for="doc-ta-1">手机</label>
			  <textarea class="" rows="1" name="mobile" id="phone"/></textarea>
			</div>

	  </fieldset>
	</form>
	<p><button class="am-btn am-btn-default" id="submit">提交</button></p>
</body>
<script>
$(function(){
	//alert("sumbit");
	$("#submit").click(function(){
		let usr = $("#usrn").val();
		let pwd = $("#pswd").val();
		let power = $("#power").val();
		let phone = $("#phone").val();
		
		if(usr == '' || pwd == '' || power == '')
			alert("请输入完整信息！");
		else
		{
			let JSONobj = {
				"userName":	usr,
				"password":	pwd,
				"isAdmin":	power,
				"mobile":	phone
			}
			
			let userInfo = JSON.stringify(JSONobj);
			//alert(userInfo);
			
			 $.post("/PHP/user/signup.php", userInfo ,
					function(data,status){
					
					let stat = data["signupStatus"];
					
					if(stat == 0)
						alert('success');
					else if(stat == 1)
						alert('username already signed');
					else if(stat == 2)
						alert('phone number already signed');
					
					location.reload(true); 
						
			});
		}
	});
	
});
</script>
</html>