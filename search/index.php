<?php
ini_set('display_errors','1');
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
						<?php
							require('lib/dir.php');
							$pwd = getcwd();
							chdir('../');
							$dirs = getDirs('.');
							chdir($pwd);
							//print_r($dir_tree);
						?>
						<div id="directory">
							<span id="selected">.</span>
							<span class="angle-down">&#10148;</span>
						</div>
						<input type="hidden" name="dir" value="." />
						<div id="dir-list">
							<ul>
								<li><span class="name" data-val=".">.</span></li>
								<?php
								$n = 0;
								foreach($dirs as $name => $dir) { ?>
									<li id="dir-<?php echo $n ?>">
									<?php if($dir['has_dir']) { ?>
										<span class="angle-down">&#10148;</span>
										<span class="name" data-val="<?php echo $dir['dir'] ?>"><?php echo $name ?></span>
									<?php } else { ?>
										<span class="name" data-val="<?php echo $dir['dir'] ?>"><?php echo $name ?></span>
									<?php } ?>
									</li>
								<?php $n++;
								} ?>
							</ul>
						</div>
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
			var dir_list = document.getElementById('dir-list');
			var angle = document.querySelector('#directory .angle-down');
			document.getElementById('dir-list').addEventListener('click', function(e) {
				var element = e.target;

				if(element.classList.contains('angle-down')) {
					var parent = element.parentElement;
					var dir = parent.querySelector('.name').dataset.val;
					var child = document.getElementById(parent.id + '-child');
					if(!child) {
						renderNextLevel(element, dir);
					} else {
						var current_state = child.style.display;
						if(current_state == 'block') {
							child.style.display = 'none';
							element.style.transform = 'rotate(0)';
						} else {
							child.style.display = 'block';
							element.style.transform = 'rotate(90deg)';
						}
					}
				} else if(element.classList.contains('name')) {
					var value = element.dataset.val;
					var text = element.innerHTML;

					document.getElementById('selected').innerHTML = text;
					document.querySelector('input[name=dir]').value = value;

					this.style.display = 'none';
					angle.style.transform = 'rotate(90deg)';
				}
			});

			document.getElementById('directory').onclick = function() {
				var current_state = dir_list.style.display;
				if(current_state == 'block') {
					dir_list.style.display = 'none';
					angle.style.transform = 'rotate(90deg)';
				} else {
					dir_list.style.display = 'block';
					angle.style.transform = 'rotate(-90deg)';
				}
			};

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

			var renderNextLevel = function(element, dir) {
				var xhr = new XMLHttpRequest();
				xhr.open('GET', 'lib/dir.php?dir=' + encodeURIComponent(dir));

				xhr.onreadystatechange = function() {
					if (xhr.readyState==4 && xhr.status==200) {
						var results = JSON.parse(xhr.responseText);
						var parent = element.parentElement;

						var ul = document.createElement('ul');

						ul.id = parent.id + '-child';
						var n = 0;
						for(name in results) {
							var li = document.createElement('li');
							li.id = parent.id + '-' + n;
							var name_span = document.createElement('span');
							name_span.className = 'name';
							name_span.dataset.val = results[name]['dir'];
							name_span.innerHTML = name;
							var arrow = document.createElement('span')
							arrow.innerHTML = '&#10148;';
							arrow.classList = 'angle-down';

							li.appendChild(name_span);
							if(results[name]['has_dir'] == 1) {
								li.insertBefore(arrow, name_span);
							}
							ul.appendChild(li);
							n++;
						}
						parent.appendChild(ul);
						document.getElementById(parent.id + '-child').style.display = 'block';
						element.style.transform = 'rotate(90deg)';
					}
				}
				xhr.send();
			}
			</script>
		<?php } ?>
		</div>
	</body>
</html>
