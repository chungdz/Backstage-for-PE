<?php
include 'userManager.php';
include '../response.php';
$response = new Response();
$response->AddDefineJson('configure.json');

if(!$_UserManager->isAdmin()) {
	$response->handleError('你没有权限删除用户');
	$response->setResponse('status', DEL_USER_STATUS_AUTHORITY_DENIED);
	$response->printResponseJson();
	exit;
}

$mysql = $_UserManager->getMysql();
$json = $_UserManager->getJson();
$id = $json['id'];

if($_UserManager->getId() == $id){
	$response->handleError('你不能删除你自己');
	$response->setResponse('status', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}

function StmtToErrMsg($stmt) {
	return 'Errno: '.$stmt->errno.' Error: '.$stmt->error;
}

function CheckExist($mysql, $response, $k, $v, $errno) {
	$query = "SELECT COUNT(*) FROM users WHERE ".$k." = ?";
	$stmt = $mysql->prepare($query);
	$stmt->bind_param('s', $v);
	$stmt->execute();
	if($stmt->errno) {
		$response->handleError(StmtToErrMsg($stmt));
		$response->setResponse('status', DEFAULT_ERRNO);
		$response->printResponseJson();
		return FALSE;
	}
	else {
		$stmt->bind_result($cnt);
		$stmt->fetch();
		if($cnt != 1) {
			$response->handleError($k.': '.$v.' not exists.');
			$response->setResponse('status', $errno);
			$response->printResponseJson();
			return FALSE;
		}
	}
	return TRUE;
}

if(!CheckExist($mysql, $response, 'id',$id, DEL_USER_STATUS_ID_NOT_EXISTS))
	exit;

$deleteQuery = "DELETE FROM users WHERE id = ?";
$stmt = $mysql->prepare($insertQuery);
$stmt->bind_param('i', $id);
$stmt->execute();
if($stmt->errno){
	$response->handleError($StmtToErrMsg($stmt));
	$response->setResponse('status', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}else {
	// 删除成功
	$response->setResponse('status', DEL_USER_STATUS_SUCCESS);
	$response->printResponseJson();
	exit;
}
?>