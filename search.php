<?php
session_start();
ini_set('display_errors', '1');
require('config.php');
require('php_search_lib.php');
if(!isset($_SESSION['session_id'])) {
    echo 'no session id';
    die;
}
$session = $_SESSION['session_id'];
$test = md5(PASS);

if($session != $test) {
    echo 'wrong session id';
    die;
}
if ($_POST['search'] && $_POST['dir']) {
	$search_string = $_POST['search'];
	$dir = $_POST['dir'];

	$search  = new Search();
	$filters = array(
		'binary' => 0, //TODO
		'extension' => $_POST['extension'],
		'name' => $_POST['name']
	);
	$search->setFilters($filters);
	$search->grepSearch($dir, $search_string);

	echo json_encode($search->getFiles());
}
?>
