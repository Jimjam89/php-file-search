<?php

if(isset($_GET['dir'])) {
	$dir = urldecode($_GET['dir']);

	$pwd = getcwd();
	chdir('../../');
	$out = getDirs($dir);
	chdir($pwd);

	header('Content-Type: application/json');
	echo json_encode($out);
}

function getDirs($dir) {
	$out = array();

	if(is_dir($dir)) {
		$pwd = getcwd();
		chdir($dir);
		$elements = glob('*');
		chdir($pwd);

		foreach($elements as $element) {
			if(is_dir($dir .'/'. $element)) {
				$out[$element] = array(
					'dir' => $dir .'/'. $element,
					'has_dir' => hasDirs($dir .'/'. $element)
				);
			}
		}
	}

	return $out;
}

function hasDirs($dir) {
	$pwd = getcwd();
	chdir($dir);
	$elements = glob('*');
	chdir($pwd);

	foreach($elements as $element) {
		if(is_dir($dir .'/'. $element)) {
			return 1;
		}
	}
	return 0;
}
