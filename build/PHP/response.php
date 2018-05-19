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
		header("Content-type:application/json");
		print json_encode($this->response);
	}
}
?>