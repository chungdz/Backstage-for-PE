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
 *		rating:xxx, (rating == -1 表示尚未被打分)
 *		userName: xxx
 *		}
 *	}
 *
 */

$result = queryDishRand($mysql, 10);

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