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
$selectQuery = "SELECT id, username, password FROM users WHERE username= ?"; //从数据库查询信息
$stmt = $mysqli->prepare($stmt,$selectQuery);
$stmt->bind_param('s',$name);
$stmt->execute();
$stmt->bind_result($result['id'], $result['username'], $result['pwd']);
$stmt->fetch();
if($stmt->fetch()) {
	if($pwd != $result['pwd'] || $name !=$result['username']){
		$response->handlerError('密码错误');
		$response->printJson();
		exit;
	}
	else{
		if($_UserManager->login($result['id'], $result['username'])){
			$response->printJson();
			exit;
		}
		else {
			$response->handlerError('登陆失败');
			$response->printJson();
			exit;
		}
	}
}else{
	$response->handlerError('用户'.$name.'不存在');
	$response->printJson();
	exit;
};
?>