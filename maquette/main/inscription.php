<?php
	
	require_once('/var/www/p1609223/BDW1/maquette/includes/fonctions.php');
	if (isset ($_POST['bValider'])){ 
		if(checkAvailability($_POST['pseudo'], $link)==1) {
			if (md5($_POST['mdp'])==md5($_POST['mdp2']))
			{
				register($_POST['pseudo'],$_POST['nom'],$_POST['prenom'],md5($_POST['mdp']), $link);
				echo "Félicitations vous êtes inscrit !";
				?> <a href="index.php">Aller se connecter</a><?php
			}
			else{
				echo "Les mots de passe saisis ne sont pas les mêmes";
				
			}
		}
		else {
			echo "Le pseudo est déjà utilisé";
			
		}

		
	}
		
		
?>

<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Page inscription</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
	<h2> Inscription </h2>
	<form action = "index.php?page=inscription" method ="POST">
		<label> Nom :</label> <input type ="text" name="nom"/></br>
		<label> Prenom :</label> <input type ="text" name="prenom"/></br>
		<label> Pseudo souhaite :</label> <input type ="text" name="pseudo"/></br>
		<label> Mot de passe : </label> <input type= "password" name="mdp"/></br>
		<label> Confirmer mot de passe : </label> <input type= "password" name="mdp2"/></br>
		<input type="submit" name="bValider" value="S'inscrire">
	
	</form>
		
</body>
</html>
