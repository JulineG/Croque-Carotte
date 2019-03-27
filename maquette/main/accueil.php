<?php
	require_once('/var/www/p1609223/BDW1/maquette/includes/fonctions.php'); 
	session_start();
	
	if(isset($_POST['name']) && isset($_POST['psw']))
	{
		if(getUser($_POST['name'],md5($_POST['psw']), $link) == true )
		{
			$pseudo = $_POST['name'];
			setConnected($pseudo, $link);
			
			$_SESSION['pseudo']=$_POST['name'];
			$_SESSION['connecte']=true;
		}
		else
		{
			echo 'Le Pseudo et le MdP ne correspondent à aucune utilisateur enregistré';
			$_POST['connexion']=NULL;
		}
	}
	if (isset ($_POST['deconnexion']))
	{	
		$pseudo = $_SESSION['pseudo'];
		setDeconnected($pseudo, $link);
		echo "Vous avez bien été déconnecté !";
		session_unset();
		session_destroy();
	}
	
	
	
?>


<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Bienvenue sur le jeu Croque Carotte </title>
  <link rel="stylesheet" href="style.css">
	
</head>
<body>
		<?php if((!isset($_POST['connexion'])) || (!isset($_SESSION['connecte']))){?>
		<div class="txt_explication">
		<p> Règles du jeu : Le jeu ’Croque Légumes’ est un jeu dans lequel au plus quatre joueurs peuvent jouer. Chaque
		joueur dispose initialement de 4 pions de même couleur.L’espace de jeu correspond à un chemin dont chaque cellule correspond à une place possible pour un pion. A l’extrémité du parcours se trouve une salade qui représente l’objectif. Le joueur qui atteint la salade le premier a gagné. </p>
		</div>
		<div class= "tableau_accueil">
		<table>
			<thead>
				<th>Nombre de personnes inscrites</th>
				<th>Nombre de partie effectuées depuis un mois</th>
		</thead>
		<tbody>			
			<tr>
			<td><?php $reponse= nbUser($link);
					$donnees=mysqli_fetch_assoc($reponse);
					echo $donnees['nbUser']; ?>
			</td>
			<td><?php $reponse= nbParties($link);
					$donnees=mysqli_fetch_assoc($reponse);
					echo $donnees['nbParties']; ?></td>
		</tr>
		</tbody>
		</table>
		</div>
		<form action="index.php" method="POST">
		<div class="connexion">
			<label for="name"><b>Nom d'utilisateur</b></label>
			<input type="text" placeholder="Entrer un nom d'utilisateur" name="name" required></br>
			<label for="psw"><b>Mot de Passe</b></label>
			<input type="password" placeholder="Entrer un mot de passe" name="psw" required>
			<button type="submit" name= "connexion" >Connexion</button>
		</div>
		</form> 
		<?php }else{?>
		<div class="txt_prevention">
			<p> Règles du jeu : Le jeu ’Croque Légumes’ est un jeu dans lequel au plus quatre joueurs peuvent jouer. Chaque
			joueur dispose initialement de 4 pions de même couleur.L’espace de jeu correspond à un chemin dont chaque cellule correspond à une place possible pour un pion. A l’extrémité du parcours se trouve une salade qui représente l’objectif. Le joueur qui atteint la salade le premier a gagné. </p>
			<div class= "tableau_accueil">
			<table>
				<thead>
					<th>Nombre de partie effectuées depuis un mois</th>
					<th>Date de la dernière partie</th>
					
				</thead>
				<tbody>			
					<tr>
					<td><?php $reponse= nbPartiesJoueur($link, $_POST['name']);
							$donnees=mysqli_fetch_assoc($reponse);
							echo $donnees['nbPartiesJoueur'];  ?>
					</td>
					<td><?php $reponse= dernierePartie($link, $_POST['name']);
						$donnees= mysqli_fetch_assoc($reponse);
						 
						
							if($donnees==false){
								echo 'Aucune partie enregistrée';
							}
							else {
								
								echo $donnees['dateCreation']; 
							}?></td>
					</tr>
				</tbody>
			</table>
			</div>
			<form action="index.php" method="GET">
			<div class="creerPartie">
				<input type="hidden" name="page" value="partie"/>
				<input type="hidden" name="pseudo" value="<?php echo $_SESSION['pseudo'];?>" />
				<input type="hidden" name="connecte" value="<?php echo $_SESSION['connecte'];?>" />
				<button type="submit" name= "creerPartie" >Creer une nouvelle partie </button>
			</div>
			</form>
			<form action="index.php" method="POST">
			<div class="deconnexion">
				<button type="submit" name= "deconnexion" >Se deconnecter </button>
			</div>
			</form>	
		<?php } ?>  
			
	
	
</body>
</html>
