<?php
session_start();
if(isset($_GET['logout'])) {
	unset($_SESSION['session_id']);
	unset($_GET['logout']);
}
?>
<html>
	<head>
		<title>PHP File Search</title>
		<link rel="stylesheet" href="assets/style.css" />
	</head>
	<body>
		<header>
			<h1>File Search</h1>
		</header>
		<div id="content">
		<?php if(!isset($_SESSION['session_id'])) { ?>
			<form action="lib/auth.php" method="POST">
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
			document.getElementById('submit').onclick = function() {

				var form = document.querySelector('#content form');
				var form_data = new FormData(form);
				var results_div = document.getElementById('results');

				var xhr = new XMLHttpRequest();
				xhr.open('POST', 'lib/search.php');
				results_div.innerHTML = 'Loading...';

				xhr.onreadystatechange = function() {
					if (xhr.readyState==4 && xhr.status==200) {
						var results = JSON.parse(xhr.responseText);

						if(results['refresh']) {
							location.reload()
						}

						var html = '';
						if(results['results']) {
							var result = results['results'];
							html += '<div id="count">' + results['count'] + ' results</div>';
							for(var i = 0; i < result.length; i++) {
								html +='<div class="result">';
								html += '<div class="result-title">' + result[i]['file'] + '</div>';
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
						results_div.innerHTML = html;
					}
				}
				xhr.send(form_data);
			}

			document.getElementById('filters').onclick = function() {
				document.getElementsByName('name')[0].value = '';
				document.getElementsByName('extension')[0].value = '';

				var filter_block = document.getElementsByClassName('filter-block')[0];

				elementToggle(filter_block);
			}

			document.addEventListener('click', function(event) {
				var element = event.target;
				if(element.classList.contains('result-title')) {
					var table = element.nextSibling;

					elementToggle(table);
				}
			})

			var elementToggle = function(element) {
				var current_state = element.offsetParent;

				if(current_state === null) {
					element.style.display = 'block';
				} else {
					element.style.display = 'none';
				}
			}
			</script>
		<?php } ?>
		</div>
	</body>
</html>
