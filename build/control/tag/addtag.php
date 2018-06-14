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
  <title>增加标签</title>
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
<script src="../bin/jsencrypt.min.js"></script>
<script src="../bin/encode.js"></script>  
</head>
<body>



	<header class="am-topbar">
	  <h1 class="am-topbar-brand">
		<a href="userinfo.php">添加标签</a>
	  </h1>

	  <button class="am-topbar-btn am-topbar-toggle am-btn am-btn-sm am-btn-success am-show-sm-only" data-am-collapse="{target: '#doc-topbar-collapse'}"><span class="am-sr-only">导航切换</span> <span class="am-icon-bars"></span></button>

	</header>

	<form class="am-form" enctype="multipart/form-data" name="form" data-am-validator>
	  <fieldset>
		
			<div class="am-form-group">
			  <label for="doc-ta-1">标签名字</label>
			  <textarea class="" rows="1" name="username" id="tagName" required/></textarea>
			</div>

	  </fieldset>
	</form>
	<p><button class="am-btn am-btn-default" id="submit">提交</button></p>
</body>
<script>
$(function(){
	//alert("sumbit");
	$("#submit").click(function(){
		let name = $("#tagName").val();
		
		if(name == '')
			alert("请输入完整信息！");
		else
		{
			let JSONobj = {
				"tagName":	name
			}
			
			let tagInfo = JSON.stringify(JSONobj);
			tagInfo = encode(tagInfo);
			//alert(tagInfo);
			
			 $.post("/PHP/tag/addTag.php", tagInfo ,
					function(data,status){
					
					let stat = data["Status"];
					
					if(stat == 0)
						alert('success');
					else 
					{
						alert(data['errMsg']);
					}
					
					location.reload(true); 
						
			});
		}
	});
	
});
</script>
</html>