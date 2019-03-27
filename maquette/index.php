<?php
	
	require_once('/var/www/p1609223/BDW1/maquette/includes/fonctions.php');
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<title>Projet BDW1</title>
	<link rel="stylesheet" media="all" type="text/css" href="./css/styles.css">
	<link rel="shortcut icon" type="image/x-icon" href="images/carotte.png" />
</head>

<body>
	<?php include('static/header.php'); ?>
	<div id="centre">
		<?php include('static/menu.php'); ?>
		<main>
		<?php
			$nomPage = 'main/accueil.php'; // page par défaut
			if(isset($_GET['page'])) { // verification du parametre "page"
				if(file_exists(addslashes('main/'.$_GET['page'].'.php'))) // le fichier existe
					$nomPage = addslashes('main/'.$_GET['page'].'.php');
					else
					$nomPage = 'includes/fatalError.php';
			}
			include($nomPage); // inclut le contenu
		?>
		</main>
	</div>
	<?php include('static/footer.php'); ?>
</body>

</html>
