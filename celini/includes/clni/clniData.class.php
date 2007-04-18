<?php
class clniData {
	function set($key,$val) {
		$method = "set$key";
		if (method_exists($this,$method)) {
			$this->$method($val);
		}
		else {
			$this->$key = $val;
		}
	}

	function get($key) {
		$method = "get$key";
		if (method_exists($this,$method)) {
			return $this->$method();
		}
		else {
			return $this->$key;
		}
	}

	function populate($data) {
		foreach($data as $key => $val) {
			$this->$key = $val;
		}
	}
}
