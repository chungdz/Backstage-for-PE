<?php
include 'helper.php';
include 'userManager.php';
include '../response.php';

$response = new Response();
$response->AddDefineJson('configure.json');

$mysql = $_UserManager->getMysql();
$mysqlCheck = new MysqlCheck($mysql);

$json = $_UserManager->getJson();

/**
 * @param json
 * 	{
 *		oldPwd:...,
 *		newPwd:...
 *	}
 * @return json
 *	{changePwdStatus： 0|1for success|failure}
 *  "CHANGE_PWD_STATUS_SUCCESS":0,
 *  "CHANGE_PWD_STATUS_FAILURE":1
 */

$oldPwd = $json['oldPwd'];
$newPwd = $json['newPwd'];
$userId = $_UserManager->getId();
if(!$userId) {
	$response->handleError('您尚未登陆');
	$response->setResponse('changePwdStatus', CHANGE_PWD_STATUS_FAILURE);
	$response->printResponseJson();
	exit;
}

if(!IsMd5($newPwd)) {
	$response->handleError($newPwd.'不是合法的MD5字符串');
	$response->setResponse('changePwdStatus', CHANGE_PWD_STATUS_FAILURE);
	$response->printResponseJson();
	exit;
}

// 检查密码是否正确
if(!$_UserManager->checkPassword($oldPwd)) {
	$response->handleError('旧密码错误');
	$response->setResponse('changePwdStatus', CHANGE_PWD_STATUS_FAILURE);
	$response->printResponseJson();
	exit;
}

// 修改数据库
$updateQuery = "UPDATE users SET password=? WHERE id=?";
$stmt = $mysql->prepare($updateQuery);
$stmt->bind_param('si', $newPwd, $userId);
$stmt->execute();
if($stmt->errno) {
	$response->handleError($StmtToErrMsg($stmt));
	$response->setResponse('changePwdStatus', CHANGE_PWD_STATUS_FAILURE);
	$response->printResponseJson();
	exit;
}

// 修改成功
$response->setResponse('changePwdStatus', CHANGE_PWD_STATUS_SUCCESS);
$response->printResponseJson();
exit;

?>