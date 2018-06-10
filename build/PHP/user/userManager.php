<?php

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
		// Modify session to update session lifetime
		$_SESSION['lastAccess'] = time();
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
	function login($userId, $username, $isAdmin) {
		if(!isset($_SESSION))
			return FALSE;
		$_SESSION['userId'] = $userId;
		$_SESSION['username'] = $username;
		$_SESSION['isAdmin'] = $isAdmin;
		return TRUE;
	}
	function logout() {
		if(!isset($_SESSION))
			return FALSE;
		if(isset($_SESSION['userId'], $_SESSION['username'])){
			unset($_SESSION['userId']);
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
	function getId() {
		if(!isset($_SESSION) || !isset($_SESSION['userId']))
			return NULL;
		return $_SESSION['userId'];
	}
	function getUsername() {
		if(!isset($_SESSION) || !isset($_SESSION['username']))
			return NULL;
		return $_SESSION['username'];
	}
	/**
	 * @param string $pwd 密码
	 * @param string $username 用户名，如果不提供则使用_SESSION['username']
	 * @return bool 用户名不存在或密码错误返回FALSE
	 */
	function checkPassword($pwd, $username = NULL) {
		if($username == NULL)
			$username = $this->getUsername();

		$query = "SELECT password FROM users WHERE username= ?";
		$stmt = $this->mysql->prepare($query);
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$stmt->bind_result($mysql_pwd);

		if($stmt->fetch()) {
			if($pwd == $mysql_pwd) {
				return TRUE;
			}
		}
		// Username does not exist or password is wrong.
		return FALSE;
	}
}
session_start();
$_UserManager = new UserManager();
?>