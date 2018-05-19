<?php

define("DEFAULT_ERRNO", 527);

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
	function AddDefineJson($filename) {
		echo $filename;
		$json = file_get_contents($filename);
		$data = json_decode($json, TRUE);
		foreach ($data as $def => $v){
			define($def, $v);
		}
	}
}
?>