<?php
define('LOGIN_STATUS_SUCCESS', 0);
define('LOGIN_STATUS_FAILURE', 1);
//返回json，0为成功，1为用户名已存在，2为手机号已存在
define('SIGNUP_STATUS_SUCCESS', 0);
define('SIGNUP_STATUS_NAME_EXISTS', 1);
define('SIGNUP_STATUS_MOBILE_EXISTS', 2);

define('DEFAULT_ERRNO', 527);

class Response {
	public $errno = 0;
	public $errmsg = '';
	public $response = [];
	function setResponse($k, $v) {
		$this->response[$k] = $v;
	}
	function handleError($errMsg, $errNo = DEFAULT_ERRNO) {
		$this->errno = $errNo;
		$this->errmsg = $errMsg;
		$this->setResponse('errMsg', $errMsg);
	}
	function printResponseJson() {
		print json_encode($this->response);
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
			$this->post = file_get_contents('php://input');
			try {
				$this->json = json_decode($this->post, TRUE);
			} catch (Exception $e) {
				$this->json = NULL;
			}
		}
		if(isset($_SESSION))
			$this->sess = $_SESSION;
		if(isset($_SERVER))
			$this->serv = $_SERVER;
	}
	function getMysql() {
		return $this->mysql;
	}
	function getJson() {
		return $this->json;
	}
	function getPost() {
		return $this->post;
	}
	function login($id, $userId, $isAdmin) {
		if(!isset($_SESSION))
			return FALSE;
		$_SESSION['user_id'] = $id;
		$_SESSION['username'] = $userId;
		$_SESSION['isAdmin'] = $isAdmin;
		return TRUE;
	}
	function logout() {
		if(!isset($_SESSION))
			return FALSE;
		if(isset($_SESSION['user_id'], $_SESSION['username'])){
			unset($_SESSION['user_id']);
			unset($_SESSION['username']);
			unset($_SESSION['isAdmin']);
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	function isAdmin() {
		if(!isset($_SESSION) || !isset($_SESSION['isAdmin']))
			return FALSE;
		return $_SESSION['isAdmin'];
	}
}
session_start();
header("Content-type:application/json");
$_UserManager = new UserManager();
?>