<!DOCTYPE html>
<html lang="fr">
<head>
	<title><?php echo $this->title ?></title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" href="<?php echo $this->router->getCSSPath() ?>">
</head>
<body>
	<nav class="menu">
		<ul>
			<?php
		foreach ($this->menu as $key => $value) {
			echo "<li><a href=\"$value\">$key</a></li>";
		}
			?>
		</ul>
	</nav>
	<main>
		<h1><?php echo $this->title; ?></h1>
		<h2><?php if ($this->feedback !== null || $this->feedback !== '') {
			echo $this->feedback;
		} ?> </h2>
		<?php echo $this->content; ?>
	</main>
</body>
</html>