<?php
include '../response.php';
include '../user/userManager.php';

$response = new Response();
$response->AddDefineJson('configure.json');

$mysql = $_UserManager->getMysql();
$json = $_UserManager->getJson();


//检查管理员权限
if(!$_UserManager->isAdmin())
{
	$response->handleError('你没有管理员权限');
	$response->setResponse('addCanteenStatus', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}

$name = $json['canteenName'];
$pic = '';
/*
上传图片upload images
if(isset($_FILES['image']))
{
	$img = $_FILES['image'];
}
*/

if($name == '')
{
	$response->handleError('食堂名不能为空');
	$response->setResponse('addCanteenStatus', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}


function StmtToErrMsg($stmt) {
	return 'Errno: '.$stmt->errno.' Error: '.$stmt->error;
}

function CheckUnique($mysql, $response, $k, $v, $errno) {
	$query = "SELECT COUNT(*) FROM canteens WHERE ".$k." = ?";
	$stmt = $mysql->prepare($query);
	$stmt->bind_param('s', $v);
	$stmt->execute();
	if($stmt->errno) {
		$response->handleError(StmtToErrMsg($stmt));
		$response->setResponse('addCanteenStatus', DEFAULT_ERRNO);
		$response->printResponseJson();
		return FALSE;
	}
	else {
		$stmt->bind_result($cnt);
		$stmt->fetch();
		if($cnt > 0) {
			$response->handleError($k.': '.$v.' already exists.');
			$response->setResponse('addCanteenStatus', $errno);
			$response->printResponseJson();
			return FALSE;
		}
	}
	return TRUE;
}
if(!CheckUnique($mysql, $response, 'canteen_name',$name, ADDCANTEEN_STATUS_NAME_EXISTS))
	exit;

$insertQuery = "INSERT INTO canteens(canteen_name,canteen_pic)VALUES(?, ?)";
$stmt = $mysql->prepare($insertQuery);
$stmt->bind_param('ss', $name, $pic);
$stmt->execute();
if($stmt->errno){
	$response->handleError($StmtToErrMsg($stmt));
	$response->setResponse('addCanteenStatus', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}else {
	// 添加成功
	$response->setResponse('addCanteenStatus', ADDCANTEEN_STATUS_SUCCESS);
	$response->printResponseJson();
	exit;
}
?>





