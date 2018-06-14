<?php
	include '../com_fun.php';
	include '../../PHP/user/userManager.php';
	header("Content-type: text/html; charset=utf-8");
	include '../config.php';
	check_log($_UserManager);
?>

<html>
<html class="js cssanimations"><head lang="en">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta charset="UTF-8">
  <title>搜索结果</title>
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
<script src="../bin/jsencrypt.min.js"></script>
<script src="../bin/encode.js"></script>

<header class="am-topbar">
  <h1 class="am-topbar-brand">
    <a href="#">通过菜品搜索管理</a>
  </h1>

  <button class="am-topbar-btn am-topbar-toggle am-btn am-btn-sm am-btn-success am-show-sm-only" data-am-collapse="{target: '#doc-topbar-collapse'}"><span class="am-sr-only">导航切换</span> <span class="am-icon-bars"></span></button>

  <div class="am-collapse am-topbar-collapse" id="doc-topbar-collapse">
  
	<ul class="am-nav am-nav-pills am-topbar-nav">
		<li class><a href="../index.php">首页</a></li>
    </ul>

  </div>
</header>

<?php
	//echo "debug\n"; 

	$page; //第N页，如果没有GET到值，默认第一页
	if(array_key_exists('Page',$_GET))
		$page=$_GET["Page"];
	else
		$page=1;
	
	
	$total_page;//总共有几页
	$begin = ($page - 1) * 10;//当前页面第一条数据的位置
	const QUAN = 10;//一页最多10条记录
	$next_page = $page + 1;
    $prev_page = $page - 1;	


	//数据库连接
	$conn=create_();
    //获取表格数据
	$Id = $_GET["Id"];
	//获得总的条数
	$sql="SELECT COUNT(*) AS count FROM ".COMMENT_TABLE." WHERE dish_id=$Id";
	$r = Query($conn,$sql)->fetch_assoc();
	$total_page = ceil($r['count']/QUAN);
	//判断页面是否正确
	if($page > $total_page || $page < 1)
	{
		$conn->close();
		die ("不存在的页面!");
	}
	//获取表格数据
	$sql="SELECT comment_id,dish_id,user_id,content FROM ".COMMENT_TABLE." where dish_id=$Id 
	ORDER BY comment_id desc LIMIT $begin,".QUAN."";
	$figure = Query($conn,$sql);
    
    /*****这是表头****/
		echo <<<EOT

        <table class="am-table am-table-striped am-table-bordered">
		<thead>
			<tr>
				<th>ID</th>
				<th>菜品ID</th>
				<th>菜品名称</th>
				<th>作者ID</th>
				<th>作者名称</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>

EOT;
    /****这是表身，循环输出****/
	//$id = $begin;//第几个
	$dishId;
	$userId;
	$realId;
	$content;
	while($row = $figure->fetch_assoc())
	{
		$dishId = $row["dish_id"];
		$userId = $row["user_id"];
		$realId = $row["comment_id"];
		$content = $row["content"];
		
		$sql="SELECT dish_name FROM ".DISH_TABLE." where dish_id=$dishId ";
		$dishName = "不存在的菜品";
		$dishFigure = $conn->query($sql);
		if ($dishFigure->num_rows > 0) {
			$dishRow = $dishFigure->fetch_assoc();
			$dishName = $dishRow["dish_name"];
		} 
		
		$sql="SELECT username FROM ".USR_TABLE." where id=$userId ";
		$userFigure = $conn->query($sql);
		$userName = "不存在的用户";
		if($userFigure->num_rows > 0)
		{
			$userRow = $userFigure->fetch_assoc();
			$userName = $userRow["username"];
		}
		
		echo <<<EOT
			<tr>
				<td> $realId </td>
				<td> $dishId </td>
				<td> $dishName </td>
				<td> $userId </td>
				<td> $userName </td>
				<td>
					<span class="am-btn am-btn-danger am-btn-xs" onclick="deletePost($realId)">删除</span>
					<span class="am-btn am-btn-success am-btn-xs" onclick="alert('$content')">浏览内容</span>
				</td>
			</tr>		
EOT;
        
	}
	
	$conn->close();
	echo <<<EOT
	</tbody>
	</table>
	
	<footer class="blog-footer">
		  <p>
		  <form action="dishsearch.php" method="get" class="am-form-horizontal am-form-inline">
		    <div class="am-form-gruop">
				<input type="hidden" name="Id" value=$Id >
			</div>
			<div class="am-form-group">
				<input class="am-form-field am-input-sm am-u-sm-3" placeholder="页数" type="text" name="Page" >
				<button type="submit" class="am-btn am-btn-default am-btn-sm">转到</button>
			</div>
		  </form>
		  <br>
			<small>
				<ul class="am-pagination"  style="text-align:center;" >
					<li><a href="dishsearch.php?Page=$prev_page&Id=$Id">&laquo; Prev</a></li>
					第 $page 页 共 $total_page 页
					<li><a href="dishsearch.php?Page=$next_page&Id=$Id">Next &raquo;</a></li>
				</ul>
			</small>
		  </p>
		</footer>
EOT;
?>
<script>
function deletePost(value){
	
	let JSONobj = {
				"id":	value
			}
			
	let Info = JSON.stringify(JSONobj);
	Info = encode(Info);
	//alert(userInfo);
	
	 $.post("/PHP/comment/deleteComment.php", Info ,
		function(data,status){
		
		let stat = data["status"];
		
		if(stat == 0)
			alert('success');
		else if(stat == 1)
			alert('no such user');
		else if(stat == 2)
			alert('no power');
		
		location.reload(true); 
			
	});
};
</script>


</body>
</html>