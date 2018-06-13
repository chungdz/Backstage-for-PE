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
	$response->handleError('你没有权限删除用户');
	$response->setResponse('status', DEL_USER_STATUS_AUTHORITY_DENIED);
	$response->printResponseJson();
	exit;
}

if($_UserManager->getId() == $id) {
	$response->handleError('你不能删除你自己');
	$response->setResponse('status', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}

$mysqlCheck->checkExist('id', $id);
if($mysqlCheck->errno) {
	$response->handleError($mysqlCheck->error);
	$response->setResponse('status', DEL_USER_STATUS_ID_NOT_EXISTS);
	$response->printResponseJson();
	exit;
}
// 检查是否已被删除
$queryDeleted = "SELECT deleted FROM users WHERE id=?";
$stmt = $mysql->prepare($queryDeleted);
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($deleted);
$stmt->fetch();
if($deleted) {
	$response->handleError("重复删除");
	$response->setResponse('status', DEL_USER_STATUS_ID_NOT_EXISTS);
	$response->printResponseJson();
	exit;
}
$stmt->close();

$deleteQuery = "UPDATE users SET deleted=true, mobile=CONCAT(id,':', mobile), username=CONCAT('已注销',':', username, ':',id) WHERE id = ?";
$stmt = $mysql->prepare($deleteQuery);
$stmt->bind_param('i', $id);
$stmt->execute();
if($stmt->errno) {
	$response->handleError($StmtToErrMsg($stmt));
	$response->setResponse('status', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}
$stmt->close();

// 删除成功
$response->setResponse('status', DEL_USER_STATUS_SUCCESS);
$response->printResponseJson();
exit;
?>