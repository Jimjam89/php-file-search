<?php

class Search {

	private $files;

	public function __construct() {
		$this->files = array();
	}

	public function getFiles() {
		return $this->files;
	}

	public function grepSearch($dir, $search) {
	    $items = scandir($dir);
	    //$files = array();
	    foreach($items as $item) {
	        if($item != '.' && $item != '..' && $item != '.DS_Store') {
	            if(is_file($dir .'/'. $item)) {
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
		$search_table = $this->generateBadCharacterTable($search_string);
		$suffix_table = $this->generateSuffix($search_string);
		print_r($suffix_table);die;
		$pos = $search_length;
		$i = $search_length;
		$c = 0;
		$n = 0;
		$l = '';
		while(!feof($f)) {
			fseek($f, $pos, 0);
			$c = fgetc($f);
			echo $c .' ';
			if(in_array($c, $search_array)) {
				$i = $search_table[$c];
				$l .= $c;
				echo '- '. $c .' ';
				$pos++;
			} else {
				$pos += $i;
				$i = sizeof($search_array);
				$l = '';
			}
			if(strlen($l) > 4) {
				//echo $l .' ';
			}
			if(strcmp($l, $search_string) == 0) {
				return true;
			}

			if($c == PHP_EOL) {
				$pos++;
			}
			$n++;
		}
		return false;
	}

	private function generateBadCharacterTable($search_string) {
		/*$string_array = str_split($search_string);
		$n = sizeof($string_array) - 1;
		$search_table = array();
		foreach($string_array as $letter) {
			if($n > 0) {
				$search_table[$letter] = $n;
				$n--;
			} else {
				$search_table[$letter] = sizeof($string_array);
			}
		}
		print_r($search_table);*/
		$l = strlen($search_string);
		$bc_table = array();

		for($i = 0; $i < $l - 1; ++$i) {
			$bc_table[$search_string{$i}] = $l - $i - 1;
		}
		//print_r($bc_table);die;
		return $bc_table;
	}

	private function generateSuffix($search_string) {
		$m = strlen($search_string);
 		$s = array();
		$s[$m - 1] = $m;
		$g = $m - 1;

		for ($i = $m - 2; $i >= 0; --$i) {
			if ($i > $g && $s[$i + $m - 1 - $f] < $i - $g) {
				$s[$i] = $s[$i + $m - 1 - $f];
			} else {
				if ($i < $g) {
					$g = $i;
				}
				$f = $i;

				while ($g >= 0 && $search_string[$g] == $search_string[$g + $m - 1 - $f]) {
					$g--;
				}
				$s[$i] = $f - $g;
			}
		}

		return $s;
	}

	private function generateGoodSuffixTable($search_string) {
		$string_array = str_split($search_string);
	}

	private function searchFile($file, $search) {
		$result = array();
		$n = 1;

		$f = fopen($file, 'r');

		while($line = fgets($f)) {
			if(preg_match('/'. preg_quote($search) .'/', $line)) {
				$result[] = array(
					'line_number' => $n,
					'line' => $line
				);
			}
			$n++;
		}

		return $result;
	}
}
?>
