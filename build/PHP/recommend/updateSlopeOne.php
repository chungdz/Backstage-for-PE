<?php
include '../user/helper.php';
include '../user/userManager.php';
include 'slopeOne.php';

$mysql = $_UserManager->getMysql();

/**
 * Update table slopeOne of MySQL
 */

// Construct user data
$userData = [];

$selectQuery = "SELECT user_id, dish_id, value FROM ratings";
$selectStmt = $mysql->prepare($selectQuery);
$selectStmt->execute();
$selectStmt->bind_result($userId, $dishId, $value);
while($selectStmt->fetch()) {
  $userData[$userId][$dishId] = $value;
}

// Construct Slope One model
$slopeOne = new SlopeOne();
$slopeOne->update($userData);
$diffs = $slopeOne->getDiffs();
$freqs = $slopeOne->getFreqs();

// Save Slope One model in MySQL
$updateQuery = "INSERT INTO slopeOne (dishId1, dishId2, diff, freq) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE diff=VALUES(diff),freq=VALUES(freq)";
$stmt = $mysql->prepare($updateQuery);

foreach($diffs as $item1 => &$ratings) {
  foreach($ratings as $item2=>$rating) {
    $diff = $diffs[$item1][$item2];
    $freq = $freqs[$item1][$item2];
    $stmt->bind_param('iidi', $item1, $item2, $diff, $freq);
    $stmt->execute();
    if($stmt->errno) {
      die($StmtToErrMsg($stmt));
    }
  }
}

// Update completes.
echo "Slope One更新成功\n";

?>