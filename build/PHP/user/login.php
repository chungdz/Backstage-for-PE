<?php
include 'helper.php';
include 'userManager.php';
include '../response.php';

$response = new Response();
$response->AddDefineJson('configure.json');

$mysql = $_UserManager->getMysql();
$mysqlCheck = new MysqlCheck($mysql);

$json = $_UserManager->getJson();
$name=$json['userName'];
$pwd=$json['password'];
// $name = "testname";
// $pwd = "testpwd";

if($name=='') {
	$response->handleError('用户名不能为空');
	$response->setResponse('loginStatus', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}
if($pwd=='') {
	$response->handleError('密码不能为空');
	$response->setResponse('loginStatus', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}

// 检查用户名是否存在
$mysqlCheck->checkExist('username', $name, FALSE);
if($mysqlCheck->errno) {
	$response->handleError($mysqlCheck->error);
	$response->setResponse('status', LOGIN_STATUS_FAILURE);
	$response->printResponseJson();
	exit;
}

// 检查密码是否正确
if(!$_UserManager->checkPassword($pwd, $name)) {
	$response->handleError('密码错误');
	$response->setResponse('loginStatus', LOGIN_STATUS_FAILURE);
	$response->printResponseJson();
	exit;
}

// 获取用户ID和权限
$selectQuery = "SELECT id, isAdmin FROM users WHERE username= ? AND deleted=false";
$stmt = $mysql->prepare($selectQuery);
$stmt->bind_param('s',$name);
$stmt->execute();
$stmt->bind_result($userID, $isAdmin);

if($stmt->fetch()) {
	if($_UserManager->login($userID, $name, $isAdmin)) {
		$response->setResponse('token', $_UserManager->getToken());
		$response->setResponse('loginStatus', LOGIN_STATUS_SUCCESS);
		$response->setResponse('userID', $userID);
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
// 以下内容不应该被执行，因为已检查过用户名存在。
// 保留以下内容以防万一。
// 以下代码在尝试登录已被删除的用户时会被调用
else {
	$response->handleError('用户'.$name.'已被删除');
	$response->setResponse('loginStatus', LOGIN_STATUS_FAILURE);
	$response->printResponseJson();
	exit;
};
?>