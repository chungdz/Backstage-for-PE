<?php
include 'helper.php';
include 'userManager.php';
include '../response.php';

$response = new Response();
$response->AddDefineJson('configure.json');

$mysql = $_UserManager->getMysql();
$mysqlCheck = new MysqlCheck($mysql);

$json = $_UserManager->getJson();
$id = $json['id'];

if(!$_UserManager->isAdmin()) {
	$response->handleError('你没有权限查看用户');
	$response->setResponse('status', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}

$mysqlCheck->checkExist('id', $id);
if($mysqlCheck->errno) {
	$response->handleError('用户不存在');
	$response->setResponse('status', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}

$deleteQuery = "SELECT id, username, mobile, deleted FROM users WHERE id = ?";
$stmt = $mysql->prepare($deleteQuery);
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($id, $username, $mobile, $deleted);
if($stmt->errno) {
	$response->handleError($StmtToErrMsg($stmt));
	$response->setResponse('status', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}
$stmt->fetch();
// 查询成功
$response->setResponse('id', $id);
$response->setResponse('username', $username);
$response->setResponse('mobile', $mobile);
$response->setResponse('deleted', $deleted);
$response->setResponse('status', 0);
$response->printResponseJson();
exit;

?>