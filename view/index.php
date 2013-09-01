<!DOCTYPE html>
<html>
<head>
	<?=$global_header?>
</head>
<body>

<div id="wrapper">

	<header class="header">
		<span class="header__h1">Simple TODO LISTS</span>
		<span class="header__h2">From Ruby Garage</span>
	</header>

	<ul class="js-projects">
	</ul>

	<footer>
		<button class="project__add js-add-project">Add TODO List</button>
	</footer>

	<button class="js-task-queries-button">SQL Task</button>
	<div class="js-task-queries task-queries" style="display: none">
		<div>SQL Task: </div>
		<br><br>
		<?php foreach($queries as $key => $query) { ?>
			<div class="query">
				<div>Query <?=$key?>: </div>
				<pre>
					<?php var_dump($query['query']); ?>
				</pre>
				<pre>
					<div>Result: </div>
					<?php var_dump($query['result']); ?>
				</pre>
			</div>
			<br><br><br>
		<?php } ?>
	</div>

</div>

</body>
</html>