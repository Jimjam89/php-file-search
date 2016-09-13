<?php
session_start();
if(isset($_GET['logout'])) {
	unset($_SESSION['session_id']);
	unset($_GET['logout']);
}
?>
<html>
	<head>
		<script src="assets/jquery-3.1.0.min.js"></script>
		<link rel="stylesheet" href="assets/style.css" />
	</head>
	<body>
		<h1>File Search</h1>
	<?php if(!isset($_SESSION['session_id'])) { ?>
		<form action="auth.php" method="POST">
			<label>Password</label>
			<input type="password" name="password"></input>
			<input type="submit"></input>
		</form>
	<?php } else { ?>
		<a href="index.php?logout=1">Logout</a>
		<form action="search.php" method="POST">
			<label>Search</label>
			<input type="text" name="search"></input>
			<label>Directory</label>
			<select name="dir">
				<option value="<?php echo '.' ?>">/</option>
				<?php
					$dirs = glob('*');
					foreach($dirs as $dir) {
						if(is_dir($dir)) { ?>
				<option value="<?php echo $dir ?>">/<?php echo $dir ?></option>
				<?php } } ?>
			</select>
			<a id="submit">Search</a>
		</form>
		<div id="results"></div>
		<script type="text/javascript">
		$('#submit').click(function() {
			$.ajax({
				url: 'search.php',
				type:'post',
				dataType:'json',
				data: $('form').serialize(),
				success: function(result) {
					html = '';
					for(var i = 0; i < result.length; i++) {
						html +='<div class="result">' + result[i]['file'] + '</div>';
					}
					$('#results').html(html);
					for(var i; i < result['grep'].length; i++) {
						if(i < result['custom'].length) {
							if(result['grep'][i] != result['custom'][i]['file']) {
								console.log(result['grep'][i]);
							}
						} else {
							console.log(result['grep'][i]);
						}
					}
				}
			});
		});
		</script>
	<?php } ?>
	</body>
</html>
