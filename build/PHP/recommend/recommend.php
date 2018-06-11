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

if(empty($userPref)) {	// 用户从未评过分，返回菜品平均值
	$selectQuery = "SELECT dish_id, AVG(value) AS value FROM ratings GROUP BY dish_id";
	$selectStmt = $mysql->prepare($selectQuery);
	$selectStmt->execute();
	$selectStmt->bind_result($dishId, $value);
	while($selectStmt->fetch()) {
	  $result[$dishId] = round ($value, 2);
	}
	$selectStmt->close();
} else {	// 用户曾经评过分，使用Slope One推荐
	// Predict user's preference
	$result = $slopeOne->predict($userPref);
}

arsort($result);
$result = array_slice($result, 0, 10, TRUE);


/**
 * @return array d
 *		dishID: xxx,
 *		canteenID: xxx, 
 *		dishName: xxx, 
 *		photo: url, 
 *		userID:xxx, 
 *		rating:xxx, 
 *		userName: xxx
 */
function queryDish($mysql, $dishId) {
	$query = "SELECT dishes.dish_id,
		dishes.canteen_id, 
		dishes.dish_name, 
		dishes.dish_pic,
		dishes.user_id,
		AVG(ratings.value),
		users.username FROM dishes, users, ratings WHERE dishes.dish_id=?
		AND (dishes.user_id=users.id OR dishes.user_id IS NULL)
		AND dishes.dish_id=ratings.dish_id";
	$stmt = $mysql->prepare($query);
	$stmt->bind_param('i', $dishId);
	$stmt->execute();
	$stmt->bind_result($d['dishID'], $d['canteenID'], $d['dishName'], 
		$d['photo'], $d['userID'], $d['rating'], $d['userName']);
	echo $dishId."\n";
	$stmt->fetch();
	$stmt->close();
	if(!$d['rating'])
		$d['rating'] = -1;
	if(!$d['userID'])
		$d['userID'] = "已注销用户";
	return $d;
}

$dishList = [];

foreach ($result as $key => $value) {
	echo $key.': '.$value."\n";
	$dishList[] = queryDish($mysql, $key);
}

$response->setResponse('status', 0);
$response->setResponse('dishNum', count($dishList));
$response->setResponse('dishList', array_values($dishList));
$response->printResponseJson();
exit;

?>