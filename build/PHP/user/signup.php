<?php
include 'helper.php';
include 'userManager.php';
include '../response.php';

$response = new Response();
$response->AddDefineJson('configure.json');

$mysql = $_UserManager->getMysql();
$mysqlCheck = new MysqlCheck($mysql);

$json = $_UserManager->getJson();
$name = $json['userName'];
$pwd = $json['password'];

// $name = "testname";
// $pwd = "testpwd";

if(isset($json['isAdmin'])) {
	if(!$_UserManager->isAdmin()) {
		$response->handleError('你没有权限设置管理员信息');
		$response->setResponse('signupStatus', DEFAULT_ERRNO);
		$response->printResponseJson();
		exit;
	}
}

if($name==''){
	$response->handleError('用户名不能为空');
	$response->setResponse('signupStatus', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}
if($pwd==''){
	$response->handleError('密码不能为空');
	$response->setResponse('signupStatus', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}

$namePattern = "/^[0-9a-zA-Z_]{6,16}$/";
if(preg_match($namePattern, $name) != 1){
	$response->handleError($name.'不是由6至16位的数字、字母或下划线组成的合法用户名');
	$response->setResponse('signupStatus', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}

if(!IsMd5($pwd)) {
	$response->handleError($pwd.'不是合法的MD5字符串');
	$response->setResponse('signupStatus', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}

$mysqlCheck->checkUnique('username', $name);
if($mysqlCheck->errno) {
	$response->handleError($mysqlCheck->error);
	$response->setResponse('signupStatus', SIGNUP_STATUS_NAME_EXISTS);
	$response->printResponseJson();
	exit;
}

$insertQuery = "INSERT INTO users(username,password)VALUES(?, ?)";
$stmt = $mysql->prepare($insertQuery);
$stmt->bind_param('ss', $name, $pwd);
$stmt->execute();
if($stmt->errno){
	$response->handleError($StmtToErrMsg($stmt));
	$response->setResponse('signupStatus', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}
// 设置管理员isAdmin
if(isset($json['isAdmin'])) {
	$isAdmin = $json['isAdmin'];
	$insertId = $stmt->insert_id;
	$adminQuery = "UPDATE users SET isAdmin = ? WHERE id = ?";
	$adminStmt = $mysql->prepare($adminQuery);
	$adminStmt->bind_param('ii', $isAdmin, $insertId);
	$adminStmt->execute();
	if($adminStmt->errno){
		$response->handleError($StmtToErrMsg($stmt));
		$response->setResponse('signupStatus', DEFAULT_ERRNO);
		$response->printResponseJson();
		exit;
	}
	else
		$response->setResponse('adminInfo', '"isAdmin" is set to '.$isAdmin);
}
// 注册成功
$response->setResponse('signupStatus', SIGNUP_STATUS_SUCCESS);
$response->printResponseJson();
exit;

?>