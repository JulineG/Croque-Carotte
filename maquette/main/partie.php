<?php
	
	require_once('/var/www/p1609223/BDW1/maquette/includes/fonctions.php');
	
	if (isset ($_GET['creerPartie']))
	{	
		$idP = creerPartie($_GET['pseudo'], $link);
	}
	
	if(isset($_GET['Valider']))
	{
		if((count($_GET['joueur'])>=2)&&(count($_GET['joueur'])<=3))
		{
			foreach($_GET['joueur'] as $val){
				ajoutJoueurAPartie($val,$link);
			
			}
			
		}else{
			echo "Nombre de joueurs choisis invalide !";
			$_GET['Valider']=NULL;
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
	<h2> Partie </h2>
	<?php if((!isset($_GET['Valider']))){?>
	<form action = "index.php" method ="GET">
		<input type="hidden" name="page" value="partie"/>
		<input type="hidden" name="partie" value="<?php echo $idP; ?>"/>
		<input type="hidden" name="pseudo" value="<?php echo $_GET['pseudo'];?>" />
		<label for="joueur"><b>Choisir au moins deux joueurs : </br></label>

		<?php $tabJoueurs = joueursInscrit($link); 
			foreach($tabJoueurs as $val){
				if( $val != $_GET['pseudo']){ ?>
				<label><?php echo $val;?><input type="checkbox" name="joueur[]" value="<?php echo $val;?>"></label></br>
				
		<?php } } ?>
	
		<input type="submit" name="Valider" value="Valider">
	</form>
	<?php } else{ ?>
	<form action = "index.php" method ="GET">
		<input type="hidden" name="page" value="jeu"/>
		<input type="hidden" name="partie" value="<?php echo $_GET['partie']; ?>"/>
		<input type="hidden" name="pseudo" value="<?php echo $_GET['pseudo'];?>" />
		<label for="couleur"><b>Choisir une couleur pour chaque joueur : </br></label>
		<?php $tabJoueurs = joueursInscrit($link); 
			foreach($_GET['joueur'] as $val){?>
				<input type="hidden" name="joueur[]" value="<?php echo $val;?>" />
				<label for="couleur"><b><?php echo $val ?> </b></label>
				<select name="couleur[]" >
           				<option value="rouge">Rouge</option>
           				<option value="violet">Violet</option>
           				<option value="bleu">Bleu</option>
          				<option value="jaune">Jaune</option>	
      				</select>
				</br>
		<?php }  ?>
		<label for="couleur"><b>Choisir une couleur pour vous : </b></label>
		<select name="couleur[]" >
           				<option value="rouge">Rouge</option>
           				<option value="violet">Violet</option>
           				<option value="bleu">Bleu</option>
          				<option value="jaune">Jaune</option>	
      		</select>
		<input type="submit" name="lancerPartie" value="C'est parti !">
	</form>
	<?php } ?>
	
</body>
</html>

