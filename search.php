<?php
session_start();
ini_set('display_errors', '1');
require('config.php');
require('php_search_lib.php');
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
	$dir = $_POST['dir'];
	$pwd = getcwd();
	chdir('../');
	$available_dirs = glob('*');
	$available_dirs[] = '.';
	if(in_array($dir, $available_dirs)) {
		$search  = new Search();
		$filters = array(
			'binary' => 0, //TODO
			'extension' => explode(',', $_POST['extension']),
			'name' => $_POST['name']
		);
		$search->setFilters($filters);
		$search->grepSearch($dir, $search_string);

		echo json_encode($search->getFiles());
	} else {
		echo json_encode(array());
	}
	chdir($pwd);
}
?>
