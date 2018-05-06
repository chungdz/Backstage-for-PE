<?php
include 'userManager.php';
$response = new Response();

if($_UserManager->logout()){
	$response->printJson();
	exit;
}
else {
	$response->handlerError('注销失败');
	$response->printJson();
	exit;
}
?>