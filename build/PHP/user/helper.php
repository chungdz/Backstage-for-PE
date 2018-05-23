<?php

function StmtToErrMsg($stmt) {
	return 'Errno: '.$stmt->errno.' Error: '.$stmt->error;
}

class MysqlCheck {
	private $mysql;
	public $errno = 0;
	public $error = NULL;
	function __construct($mysql) {
		$this->mysql = $mysql;
	}
	function checkExist($k, $v) {
		$query = "SELECT COUNT(*) FROM users WHERE ".$k." = ?";
		$stmt = $this->mysql->prepare($query);
		$stmt->bind_param('i', $v);
		$stmt->execute();
		if($stmt->errno) {
			$this->errno = $stmt->errno;
			$this->error = StmtToErrMsg($stmt);
			return $this->errno;
		}
		else {
			$stmt->bind_result($cnt);
			$stmt->fetch();
			if($cnt != 1) {
				$this->errno = 1;
				$this->error = $k.': '.$v.' not exists.';
				return $this->errno;
			}
		}
		$this->errno = 0;
		$this->error = NULL;
		return $this->errno;
	}

	function checkUnique($k, $v) {
		$query = "SELECT COUNT(*) FROM users WHERE ".$k." = ?";
		$stmt = $this->mysql->prepare($query);
		$stmt->bind_param('s', $v);
		$stmt->execute();
		if($stmt->errno) {
			$this->errno = $stmt->errno;
			$this->error = StmtToErrMsg($stmt);
			return $this->errno;
		}
		else {
			$stmt->bind_result($cnt);
			$stmt->fetch();
			if($cnt > 0) {
				$this->errno = 1;
				$this->error = $k.': '.$v.' already exists.';
				return $this->errno;
			}
		}
		$this->errno = 0;
		$this->error = NULL;
		return $this->errno;
	}
}

?>