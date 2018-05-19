<?php
include 'userManager.php';
include '../response.php';
$response = new Response();
$response->AddDefineJson('configure.json');

$mysql = $_UserManager->getMysql();
$json = $_UserManager->getJson();
$name=$json['userName'];
$pwd=$json['password'];
// $name = "testname";
// $pwd = "testpwd";

if($name==''){
	$response->handleError('用户名不能为空');
	$response->setResponse('loginStatus', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}
if($pwd==''){
	$response->handleError('密码不能为空');
	$response->setResponse('loginStatus', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}

$selectQuery = "SELECT id, username, password, isAdmin FROM users WHERE username= ?"; //从数据库查询信息
$stmt = $mysql->prepare($selectQuery);
$stmt->bind_param('s',$name);
$stmt->execute();
$stmt->bind_result($result['id'], $result['username'], $result['pwd'], $result['isAdmin']);

if($stmt->fetch()) {
	if($pwd != $result['pwd']){
		$response->handleError('密码错误');
		$response->setResponse('loginStatus', LOGIN_STATUS_FAILURE);
		$response->printResponseJson();
		exit;
	}
	else{
		if($_UserManager->login($result['id'], $result['username'], $result['isAdmin'])){
			$response->setResponse('loginStatus', LOGIN_STATUS_SUCCESS);
			$response->setResponse('userID', $result['id']);
			$response->printResponseJson();
			exit;
		}
		else {
			$response->handleError('登陆失败');
			$response->setResponse('loginStatus', LOGIN_STATUS_FAILURE);
			$response->printResponseJson();
			exit;
		}
	}
}
else {
	$response->handleError('用户'.$name.'不存在');
	$response->setResponse('loginStatus', LOGIN_STATUS_FAILURE);
	$response->printResponseJson();
	exit;
};
?>