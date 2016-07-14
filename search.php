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
	$pwd = shell_exec('pwd');
	$search_string = $_POST['search'];
	$dir = $_POST['dir'];
	exec('grep -rli --exclude "*.jpg" "'. $search_string .'" '. $dir, $results);

	echo sizeof($results);
	$search  = new Search();

	$search->grepSearch($dir, $search_string);

	$c = 0;
	foreach($search->getFiles() as $result) {
		$c += sizeof($result['result']);
	}

	echo ' - '. sizeof($search->getFiles());

	echo json_encode(array(
		'custom' => $search->getFiles(),
		'grep' => $results
	));
}
?>
