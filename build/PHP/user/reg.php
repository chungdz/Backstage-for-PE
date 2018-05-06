<?php
include 'userManager.php';
$response = new Response();

$mysql = $_UserManager->getMysql();
$json = $_UserManager->getJson();
$name=$json['name'];
$pwd=$json['pwd'];

if($name==''){
	$response->handlerError('用户名不能为空');
	$response->printJson();
	exit;
}
if($pwd==''){
	$response->handlerError('密码不能为空');
	$response->printJson();
	exit;
}

$pattern = "/^[0-9a-zA-Z_]{1,}$/";
if(preg_match($pattern, $name) != 1){
	$response->handlerError('用户名只能为数字、字母、下划线');
	$response->printJson();
	exit;
}
if(preg_match($pattern, $pwd) != 1){
	$response->handlerError('密码只能为数字、字母、下划线');
	$response->printJson();
	exit;
}
if(strlen($name) < 6 || 16 < strlen($name)){
	$response->handlerError('用户名长度应为6至16个字符');
	$response->printJson();
}
if(strlen($pwd) < 6 || 16 < strlen($pwd)){
	$response->handlerError('密码长度应为6至16个字符');
	$response->printJson();
	exit;
}

$insertQuery = "INSERT INTO users(username,password)VALUES(?, ?)";
$stmt = $mysqli->prepare($insertQuery);
$stmt->bind_param('ss', $name, $pwd);
$stmt->execute();

if($stmt->errno){
	$response->handlerError('增加用户出错，Errno: '.$stmt->errno.'. Error: '. $stmt->error);
	$response->printJson();
	exit;
}else {
	// 注册成功
	$response->printJson();
	exit;
}
?>