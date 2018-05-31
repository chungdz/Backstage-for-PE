<?php

	error_reporting(E_ALL);
	
	function check_log($_UserManager)
	{	
		if (!$_UserManager->isAdmin())
		{
			header('Location: '.'/control/login.php');
		}
	}

?>
<!--创建链接--!>
<?php
function create_()
{
	$servername = HOST;
	$username = USR;
	$password = PWD;
	$dbname = M_DB;

	// 创建连接
	$conn = new mysqli($servername, $username, $password, $dbname);
	// 检测连接
	if ($conn->connect_error) {
		die("连接失败: " . $conn->connect_error);
	}
	
	return $conn;
}
?>

<!--根据sql返回结果--!>
<?php
function Query($conn,$sql)
{
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		return $result;
	} 
	else {
		echo "query 无结果";
	}
}
?> 
<?php
function parse_file($index,&$file_type)
{
	$allowed_pic = array("gif", "jpeg", "jpg", "png");
	$allowed_mic = array("mp3", "wav");
	$temp = explode(".", $_FILES["files"]["name"][$index]);
	$extension = end($temp);     // 获取文件后缀名
	if ((($_FILES["files"]["type"][$index] == "image/gif")
	|| ($_FILES["files"]["type"][$index] == "image/jpeg")
	|| ($_FILES["files"]["type"][$index] == "image/jpg")
	|| ($_FILES["files"]["type"][$index] == "image/pjpeg")
	|| ($_FILES["files"]["type"][$index] == "image/x-png")
	|| ($_FILES["files"]["type"][$index] == "image/png"))
	&& ($_FILES["files"]["size"][$index] < 2048000)   // 小于 2M
	&& in_array($extension, $allowed_pic))
	{
		$file_type = "pic";
		return $extension;
	}
	else if(
	(($_FILES["files"]["type"][$index] == "audio/mpeg")|| 
	($_FILES["files"]["type"][$index] == "audio/x-wav")||
	($_FILES["files"]["type"][$index] == "audio/x-mpeg")||
	($_FILES["files"]["type"][$index] == "audio/mp3"))&&
	($_FILES["files"]["size"][$index] < 20480000) &&
	in_array($extension, $allowed_mic))
	{
		$file_type = "mic";
		return $extension;
	}
	else
	{
		echo "文件格式非法！";
		return null;
	}
}
?>

<?php
//上传图片
function up_files($extension,$index,$file_type,$id)
{
	echo "上传文件名: " . $_FILES["files"]["name"][$index] . "<br>";
	echo "文件类型: " . $_FILES["files"]["type"][$index] . "<br>";
	echo "文件大小: " . ($_FILES["files"]["size"][$index] / 1024) . " kB<br>";
	echo "文件临时存储的位置: " . $_FILES["files"]["tmp_name"][$index] . "<br>";
	echo "file_type: ".$file_type."<br/>";
	
	$file_url = null;
	if($file_type == "pic")
		$file_url = "pic_upload/";
	else if($file_type == "mic")
		$file_url = "audio/";
	
	//得到目录和文件名
	$url = $file_url. $id. ".". $extension;
	//上传文件
	if(move_uploaded_file($_FILES["files"]["tmp_name"][$index], $url))
		echo "文件存储在: " . $url."<br/>";
	else 
		echo "上传失败<br/>";
	//返回地址
	return $url;
}
?>

<?php
function max_id($conn)
{
	$next_id;
	$sql="select SQL_NO_CACHE max(id) as maxid from ".M_TABLE."";
	//执行sql
	$result = $conn->query($sql);
	//对结果进行判断
	if($result->num_rows > 0){
		$r = $result->fetch_assoc();
		$next_id=$r['maxid'];
		echo "next_id=".$next_id."<br>";
	}
	else
	{
		 $next_id=0;
	}
	return $next_id;
}
?>

<?php
function max_usr_id($conn)
{
	$next_id;
	$sql="select SQL_NO_CACHE max(id) as maxid from ".USR_TABLE."";
	//执行sql
	$result = $conn->query($sql);
	//对结果进行判断
	if($result->num_rows > 0){
		$r = $result->fetch_assoc();
		$next_id=$r['maxid'];
	}
	else
	{
		 $next_id=0;
	}
	return $next_id;
}
?>