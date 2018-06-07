<?php
include '../user/helper.php';
include '../user/userManager.php';
include '../response.php';
include 'slopeOne.php';

$response = new Response();
$response->AddDefineJson('configure.json');

$mysql = $_UserManager->getMysql();
$mysqlCheck = new MysqlCheck($mysql);

$json = $_UserManager->getJson();

/**
 * @param json
 * 	{
 *
 *	}
 * @return json
 *	{
 *	
 *	}
 *
 */

$userId = $_UserManager->getId();
if(!$userId) {
	$response->handleError('您尚未登陆');
	$response->setResponse('status', DEFAULT_ERRNO);
	$response->printResponseJson();
	exit;
}

// Construct Slope One model
$slopeOne = new SlopeOne();
$diffs = [];
$freqs = [];

$selectQuery = "SELECT dishId1, dishId2, diff, freq FROM slopeOne";
$selectStmt = $mysql->prepare($selectQuery);
$selectStmt->execute();
$selectStmt->bind_result($dishId1, $dishId2, $diff, $freq);
while($selectStmt->fetch()) {
  $diffs[$dishId1][$dishId2] = $diff;
  $freqs[$dishId1][$dishId2] = $freq;
}
$selectStmt->close();

$slopeOne->setModel($diffs, $freqs);

// Construct user's preference
$userPref = [];

$selectQuery = "SELECT dish_id, value FROM ratings WHERE user_id = $userId";
$selectStmt = $mysql->prepare($selectQuery);
$selectStmt->execute();
$selectStmt->bind_result($dishId, $value);
while($selectStmt->fetch()) {
  $userPref[$dishId] = $value;
}
$selectStmt->close();

// Predict user's preference
$result = $slopeOne->predict($userPref);

var_dump($result);
// 修改成功
// $response->setResponse('status', 0);
// $response->printResponseJson();
// exit;

?>