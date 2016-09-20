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
		<header>
			<h1>File Search</h1>
		</header>
		<div id="content">
		<?php if(!isset($_SESSION['session_id'])) { ?>
			<form action="auth.php" method="POST">
				<label>Password</label>
				<input type="password" name="password"></input>
				<input type="submit"></input>
			</form>
		<?php } else { ?>
			<a href="index.php?logout=1">Logout</a>
			<form action="search.php" method="POST">
				<div class="row">
					<div class="form-element">
						<label>Search</label>
						<input type="text" name="search"></input>
					</div>
					<div class="form-element">
						<label>Directory</label>
						<select name="dir">
							<option value=".">/</option>
							<?php
								$pwd = getcwd();
								chdir('../');
								$dirs = glob('*');
								foreach($dirs as $dir) {
									if(is_dir($dir)) { ?>
							<option value="<?php echo $dir ?>">/<?php echo $dir ?></option>
							<?php } }
							chdir($pwd);
							?>
						</select>
					</div>
					<div class="form-element">
						<a id="submit">Search</a>
					</div>
					<div class="form-element">
						<a id="filters">Exclusions</a>
					</div>
					<div class="filter-block">
						<div class="form-element">
							<label>Name (accepts regex)</label>
							<input type="text" name="name" />
						</div>
						<div class="form-element">
							<label>Extension (e.g. jpg,txt,doc)</label>
							<input type="text" name="extension" />
						</div>
					</div>
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
					beforeSend:function() {
						$('#results').html('Loading...');
					},
					success: function(result) {
						if(result['refresh']) {
							location.reload()
						}

						html = '';
						if(result.length > 0) {
							for(var i = 0; i < result.length; i++) {
								html +='<div class="result">';
								html += '<div>' + result[i]['file'] + '</div>';
								html += '<table class="result-detail">';
								html += '<thead><tr><td>Line</td><td>Content</td></tr></thead><tbody>';
								for(var j = 0; j < result[i]['result'].length; j++) {
									html += '<tr><td>' + result[i]['result'][j]['line_number'] + '</td>';
									html += '<td>' + result[i]['result'][j]['line'] + '</td></tr>';
								}
								html += '</tbody></table>';
								html += '</div>';
							}
						} else {
							html = 'No Results';
						}
						$('#results').html(html);
					}
				});
			});

			$('#filters').click(function() {
				$('.filter-block input').val('');
				$('.filter-block').toggle();
			});

			$(document).on('click', '.result > div', function() {
				$(this).next('.result-detail').toggle();
			});
			</script>
		<?php } ?>
		</div>
	</body>
</html>
