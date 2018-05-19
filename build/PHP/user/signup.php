<?php
include 'userManager.php';
include '../response.php';
$response = new Response();
$response->AddDefineJson('configure.json');

$mysql = $_UserManager->getMysql();
$json = $_UserManager->getJson();
$name = $json['userName'];
$pwd = $json['password'];
if(isset($json['mobile']) && $json['mobile'] != '')
	$mobile = $json['mobile'];

if(isset($json['isAdmin'])) {
	if(!$_UserManager->isAdmin()) {
		$response->handleError('你没有权限设置管理员信息');
		$response->setResponse('signupStatus', DEFAULT_ERRNO);
		$response->printResponseJson();
		exit;
	}
}

// $name = "testname";
// $pwd = "testpwd";
// $mobile = "12345678910";

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

$pattern = "/^[0-9a-zA-Z_]{1,}$/";
if(preg_match($pattern, $name) != 1){
	$response->handleError('用户名只能为数字、字母、下划线');
	$response->setResponse('signupStatus', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}
if(preg_match($pattern, $pwd) != 1){
	$response->handleError('密码只能为数字、字母、下划线');
	$response->setResponse('signupStatus', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}
if(strlen($name) < 6 || 16 < strlen($name)){
	$response->handleError('用户名长度应为6至16个字符');
	$response->setResponse('signupStatus', DEFAULT_ERRNO);
	$response->printResponseJson();
}
if(strlen($pwd) < 6 || 16 < strlen($pwd)){
	$response->handleError('密码长度应为6至16个字符');
	$response->setResponse('signupStatus', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}

function StmtToErrMsg($stmt) {
	return 'Errno: '.$stmt->errno.' Error: '.$stmt->error;
}

function CheckUnique($mysql, $response, $k, $v, $errno) {
	$query = "SELECT COUNT(*) FROM users WHERE ".$k." = ?";
	$stmt = $mysql->prepare($query);
	$stmt->bind_param('s', $v);
	$stmt->execute();
	if($stmt->errno) {
		$response->handleError(StmtToErrMsg($stmt));
		$response->setResponse('signupStatus', DEFAULT_ERRNO);
		$response->printResponseJson();
		return FALSE;
	}
	else {
		$stmt->bind_result($cnt);
		$stmt->fetch();
		if($cnt > 0) {
			$response->handleError($k.': '.$v.' already exists.');
			$response->setResponse('signupStatus', $errno);
			$response->printResponseJson();
			return FALSE;
		}
	}
	return TRUE;
}

if(!CheckUnique($mysql, $response, 'username',$name, SIGNUP_STATUS_NAME_EXISTS))
	exit;
if(isset($mobile) && !CheckUnique($mysql, $response, 'mobile',$mobile, SIGNUP_STATUS_MOBILE_EXISTS))
	exit;

$insertQuery = "INSERT INTO users(username,password,mobile)VALUES(?, ?, ?)";
$stmt = $mysql->prepare($insertQuery);
$stmt->bind_param('sss', $name, $pwd, $mobile);
$stmt->execute();
if($stmt->errno){
	$response->handleError($StmtToErrMsg($stmt));
	$response->setResponse('signupStatus', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}else {
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
}
?>