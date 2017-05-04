<?php

class SearchLog {
	private $file;
	private $status;
	private $start;

	public function __construct($file) {
		$this->file = fopen($file, 'w');
		$this->status = true;
	}

	public function setStatus($status) {
		$this->status = $status;
	}

	public function setStartTime($time) {
		$this->start = $time;
	}

	public function write($message) {
		if($this->status) {
			$time = microtime(true) - $this->start;
			fwrite($this->file, $time .'s - '. $message ."\n");
		}
	}
}
