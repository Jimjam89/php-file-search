<?php
session_start();
ini_set('display_errors', '1');
require('../config.php');
require('php_search_lib.php');
header('Content-Type: application/json');
if(!isset($_SESSION['session_id'])) {
    echo json_encode(array('refresh' => true));
    die;
}
$session = $_SESSION['session_id'];
$test = md5(PASS);

if($session != $test) {
    echo json_encode(array('refresh' => true));
    die;
}
if ($_POST['search'] && $_POST['dir']) {
	$search_string = $_POST['search'];
	$dir = str_replace('../', '', $_POST['dir']);
	$pwd = getcwd();
	chdir('../../');
	$available_dirs = glob('*');
	$available_dirs[] = '.';
	if(is_dir($dir)) {
		if(!empty($_POST['extension']) || !empty($_POST['name'])) {
			$filters = array(
				'binary' => 0, //TODO
				'extension' => explode(',', $_POST['extension']),
				'name' => $_POST['name']
			);
		} else {
			$filters = array();
		}

		if(is_callable('exec') && false === stripos(ini_get('disable_functions'), 'exec')) {
			$query = 'grep -nr';
			if(!empty($filters)) {
				if($filters['binary']) {

				}
				$query .= ' --exclude={';
				if($filters['extension']) {
					foreach($filters['extension'] as $ext) {
						$exts[] = '"*.'. $ext .'"';
					}
					$query .= implode(',', $exts) .',';
				}
				if($filters['name']) {
					$query .= '"'. $filters['name'] .'"}';
				} else {
					$query .= '}';
				}
			}

			$query .= ' "'. $search_string .'" '. $dir;

			exec($query, $results);
			$tmp = array();
			$files = array();
			foreach($results as $result) {
				$result_array = explode(':', $result);
				if(isset($result_array[2])) {
					$tmp[$result_array[0]][] = array(
						'line_number' => $result_array[1],
						'line' => htmlspecialchars($result_array[2])
					);
				}
			}

			foreach($tmp as $file => $value) {
				$files[] = array(
					'file' => $file,
					'result' => $value
				);
			}
			$count = sizeof($results);
		} else {
			$search  = new Search();
			$search->setFilters($filters);
			$search->setSearch($search_string);
			$search->grepSearch($dir);
			$files = $search->getFiles();
			$count = $search->getCount();
		}

		echo json_encode(array(
			'results' => $files,
			'count' => $count,
		));
	} else {
		echo json_encode(array());
	}
	chdir($pwd);
}
?>
