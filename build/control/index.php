<?php
	include 'com_fun.php';
	include '../PHP/user/userManager.php';
	header("Content-type: text/html; charset=utf-8"); 
	include 'config.php';
	check_log($_UserManager);
?>


<!DOCTYPE html>
<html class="js cssanimations"><head lang="en">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta charset="UTF-8">
  <title>管理</title>
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
</head>
<body>

<script src="http://apps.bdimg.com/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="http://cdn.amazeui.org/amazeui/2.7.2/js/amazeui.min.js"></script>



		<header class="am-topbar">
		  <h1 class="am-topbar-brand">
			<a href="#">管理选择</a>
		  </h1>

		  <button class="am-topbar-btn am-topbar-toggle am-btn am-btn-sm am-btn-success am-show-sm-only" data-am-collapse="{target: '#doc-topbar-collapse'}"><span class="am-sr-only">导航切换</span> <span class="am-icon-bars"></span></button>
		  
		</header>

		<table class="am-table am-table-striped am-table-bordered">
				<tbody>
					<tr>
						<td> <a href="user/usrinfo.php" target="_blank"> 用户管理 </a> </td>
					</tr>
					<tr>
						<td> <a href="canteen/canteeninfo.php" target="_blank"> 食堂管理 </a> </td>
					</tr>
					<tr>
						<td> <a href="dish/dishinfo.php" target="_blank"> 菜品管理 </a> </td>
					</tr>
					<tr>
						<td> <a href="comment/commentinfo.php" target="_blank"> 评论管理 </a> </td>
					</tr>
					<tr>
						<td> <a href="tag/taginfo.php" target="_blank"> 标签管理 </a> </td>
					</tr>
				</tbody>
		</table>
</body>
</html>