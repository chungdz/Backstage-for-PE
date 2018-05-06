<?php
class Response {
	public $errno = 0;
	public $error = '';
	public $response = [];
	function handleError($errMsg) {
		$this->errno = 1;
		$this->error = $errMsg;
		$response['error'] = $errMsg;
	}
	function printJson() {
		print json_encode($this);
	}
}
class UserManager {
	private $mysql = NULL;
	private $json = NULL;
	private $post = NULL;
	private $sess = NULL;
	private $serv = NULL;
	function UserManager() {
		$this->mysql = new mysqli('localhost', 'root', 'root', "SHIKE");
		if(isset($_POST)) {
			$this->post = $_POST;
			$this->json = json_decode($_POST['json']);
		}
		if(isset($_SESSION))
			$this->sess = $_SESSION;
		if(isset($_SERVER))
			$this->serv = $_SERVER;
	}
	function getMysql() {
		return $mysql;
	}
	function getJson() {
		return $json;
	}
	function login($id, $userId) {
		if(!isset($_SESSION))
			return FALSE;
		$_SESSION['user_id'] = $id;
		$_SESSION['username'] = $userId;
		return TRUE;
	}
	function logout() {
		if(isset($_SESSION['user_id'], $_SESSION['username'])){
			unset($_SESSION['user_id']);
			unset($_SESSION['username']);
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
}
session_start();
$_UserManager = new UserManager();
?>