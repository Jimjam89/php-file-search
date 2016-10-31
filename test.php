<?php
require 'php_search_lib.php';

$search_string = 'session';
$dir = '.';

$search  = new Search();
/*if(!empty($_POST['extension']) || !empty($_POST['name'])) {
	$filters = array(
		'binary' => 0, //TODO
		'extension' => explode(',', $_POST['extension']),
		'name' => $_POST['name']
	);
}*/
$filters = array();

$search->setFilters($filters);
$search->setSearch($search_string);
$start = microtime(true);
$search->grepSearch($dir);
$php_time = microtime(true) - $start;
$f_count = 0;
foreach($search->getFiles() as $file) {
	$f_count += sizeof($file['result']);
}

$start = microtime(true);
exec('grep -r "'. $search_string .'" '. $dir, $grep);
$grep_time = microtime(true) - $start;

?>
<html>
	<head></head>
	<body>
		<div>
			<h2>PHP GREP</h2>
			Number of Results: <?php echo $f_count ?><br />
			Time: <?php echo $php_time ?>s
		</div>
		<div>
			<h2>GREP</h2>
			Number of Results: <?php echo sizeof($grep) ?><br />
			Time: <?php echo $grep_time ?>s
		</div>
	</body>
</html>
