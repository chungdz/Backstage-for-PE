<?php
include '../user/helper.php';
include '../user/userManager.php';
include '../response.php';
include 'slopeOne.php';
include 'recommendHelper.php';

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
 *   "status": 0/1
 *   "dishNum"（菜品数量）:xx
 *   "dishList":一个json数组，数组元素是
 *		{
 *		dishID: xxx,
 *		canteenID: xxx, 
 *		dishName: xxx, 
 *		photo: url, 
 *		userID:xxx, 
 *		rating:xxx, 
 *		userName: xxx
 *		}
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

$userPref = queryUserPref($mysql, $userId);

$avg = queryDishAvg($mysql);

$result = $slopeOne->predict($userPref);

foreach ($avg as $key => $value) {
	if(!isset($result[$key]) && !isset($userPref[$key]))
		$result[$key] = $value;
}

arsort($result);
$result = array_slice($result, 0, 10, TRUE);

$dishList = [];

foreach ($result as $key => $value) {
	$dishList[] = queryDish($mysql, $key);
}

$response->setResponse('status', 0);
$response->setResponse('dishNum', count($dishList));
$response->setResponse('dishList', array_values($dishList));
$response->printResponseJson();
exit;


?>