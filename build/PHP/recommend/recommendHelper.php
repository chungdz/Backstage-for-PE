<?php
/**
 * @return array $d
 *		dishID: xxx,
 *		canteenID: xxx, 
 *		dishName: xxx, 
 *		photo: url, 
 *		userID:xxx, 
 *		rating:xxx, (rating == -1 表示尚未被打分)
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

	$stmt->fetch();
	$stmt->close();
	if(!$d['rating'])
		$d['rating'] = -1;
	if(!$d['userID'])
		$d['userID'] = "已注销用户";
	return $d;
}

/**
 * Query user's ratings of all dishes.
 * @param object $mysql
 * @param int $userId
 * @return array $userPref (dishId => user's rating)
 */
function queryUserPref($mysql, $userId) {
	$userPref = [];
	$query = "SELECT dish_id, value FROM ratings WHERE user_id = $userId";
	$stmt = $mysql->prepare($query);
	$stmt->execute();
	$stmt->bind_result($dishId, $value);
	while($stmt->fetch()) {
	  $userPref[$dishId] = $value;
	}
	$stmt->close();
	return $userPref;
}

/**
 * Query average score of every dish.
 * @param object $mysql
 * @param int $top
 * @return array $avg (dishId => average value)
 */
function queryDishAvg($mysql, $top = 10) {
	$avg = [];
	$query = "SELECT dish_id, AVG(value) AS _v FROM ratings GROUP BY dish_id ORDER BY _v DESC LIMIT $top";
	$stmt = $mysql->prepare($query);
	$stmt->execute();
	$stmt->bind_result($dishId, $value);
	while($stmt->fetch()) {
	  $avg[$dishId] = round ($value, 2);
	} 
	$stmt->close();
	return $avg;
}

/**
 * Query random dish id.
 * @param object $mysql
 * @param int $top
 * @return array $rand (dishId => dishId)这里只是为了和前一个函数统一
 */
function queryDishRand($mysql, $top = 10) {
	$rand = [];
	$query = "SELECT dish_id FROM dishes ORDER BY RAND() LIMIT $top";

	$stmt = $mysql->prepare($query);
	$stmt->execute();
	$stmt->bind_result($dishId);
	while($stmt->fetch()) {
	  $rand[$dishId] = $dishId;
	} 
	$stmt->close();
	return $rand;
}

?>