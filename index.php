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
			<a id="submit">Search</a>
			<a id="filters">Filters</a>
			<div class="filter-block">
				<label>Name</label>
				<input type="text" name="name" />
				<label>Extension</label>
				<input type="text" name="extension" />
				<label>Directory</label>
				<select name="dir">
					<option value=".">/</option>
					<?php
						$dirs = glob('*');
						foreach($dirs as $dir) {
							if(is_dir($dir)) { ?>
					<option value="<?php echo $dir ?>">/<?php echo $dir ?></option>
					<?php } } ?>
				</select>
			</div>
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
						html +='<div class="result">';
						html += '<span>' + result[i]['file'] + '</span>';
						html += '<div class="result-detail">';
						console.log(result[i]['result'][0]);
						for(var j = 0; j < result[i]['result'].length; j++) {
							html += '<div class="line"<span class="line-number">' + result[i]['result'][j]['line_number'] + '</span>';
							html += '<span class="content">' + result[i]['result'][j]['line'] + '</span></div>';
						}
						html += '</div>';
						html += '</div>';
					}
					$('#results').html(html);
				}
			});
		});

		$('#filters').click(function() {
			$('.filter-block').slideToggle();
		});

		$(document).on('click', '.result > span', function() {
			$(this).next('.result-detail').slideToggle();
		});
		</script>
	<?php } ?>
	</body>
</html>
