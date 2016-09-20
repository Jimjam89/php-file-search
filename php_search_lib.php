<?php

class Search {

	private $files;

	private $filters;

	public function __construct() {
		$this->files = array();
		$this->filters =  array();
	}

	public function getFiles() {
		return $this->files;
	}

	public function setFilters($filters) {
		$this->filters = $filters;
	}

	public function grepSearch($dir, $search) {
	    $items = scandir($dir);
	    //$files = array();
	    foreach($items as $item) {
	        if($item != '.' && $item != '..' && $item != '.DS_Store') {
	            if(is_file($dir .'/'. $item) && $this->filterFile($dir .'/'. $item)) {
					if($this->inFile($dir .'/'. $item, $search)) {
						$this->files[] = array(
							'file' => $dir .'/'. $item,
							'result' => $this->searchFile($dir .'/'. $item, $search)
						);
					}
	            } else if(is_dir($dir .'/'. $item)) {
					$this->grepSearch($dir .'/'. $item, $search);
	            }
	        }
	    }
	}

	private function inFile($file, $search_string) {
		$f = fopen($file, 'r');

		$search_array = str_split($search_string);
		$search_length = sizeof($search_array) - 1;
		$search_table = $this->generateSearchTable($search_string);
		$pos = $search_length + 1;
		$i = $search_length;
		$c = 0;
		$j = 0;
		$n = $search_length;
		$l = '';

		while(!feof($f)) {
			fseek($f, $pos, SEEK_SET);
			$c = fgetc($f);
			if(in_array($c, $search_array)) {
				if($c == $search_array[$n]) {
					$l .= $c;
					$pos--;
					$j++;
					$n--;
				} else {
					$i = $search_table[$c];
					$l = '';
					$pos += $search_table[$c] + $j;
					$n = $search_length;
					$j = 0;
				}
			} else {
				$pos += ($search_length + 1 + $j);
				$i = sizeof($search_array);
				$l = '';
				$j = 0;
				$n = $search_length;
			}
			if(strcmp(strrev($l), $search_string) == 0) {
				return true;
			}
		}
		return false;
	}

	private function searchFile($file, $search) {
		$result = array();
		$n = 1;

		$f = fopen($file, 'r');

		while($line = fgets($f)) {
			if(preg_match('/'. preg_quote($search) .'/', $line)) {
				$result[] = array(
					'line_number' => $n,
					'line' => htmlspecialchars($line)
				);
			}
			$n++;
		}

		return $result;
	}

	private function generateSearchTable($search_string) {
		$out = array();
		$search_string_array = str_split($search_string);
		$n = strlen($search_string) - 1;
		for($i = 0; $i < sizeof($search_string_array) - 1; $i++) {
			$out[$search_string_array[$i]] = $n;
			$n--;
		}
		$out[$search_string_array[$i]] = strlen($search_string);
		return $out;
	}

	private function filterFile($file) {
		/* Returns True if file passes filters */
		if(empty($this->filters)) {
			return true;
		} else {
			if($this->filters['binary']) {
				//TODO
			}

			if($this->filters['extension'] && in_array(pathinfo($file, PATHINFO_EXTENSION),$this->filters['extension'])) {
				return false;
			}

			if($this->filters['name'] && preg_match('/'. $this->filters['name'] .'/', $file)) {
				return false;
			}
			return true;
		}
	}
}
?>
