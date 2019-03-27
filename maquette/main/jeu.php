<?php
	
	require_once('/var/www/p1609223/BDW1/maquette/includes/fonctions.php');

	
	if(tableVide($link, "projet__Chemin")){
		$idChemin=1;
	} else {
		$query= "SELECT C.* FROM projet__Chemin C WHERE C.idP != '".$_GET['partie']."' ORDER BY C.idChemin DESC";
		echo $query;
		$result= executeQuery($link, $query);
		$idC=mysqli_fetch_assoc($result);
		
		$idChemin = $idC['idChemin']+1;
	}
	
	creerChemin($link, $_GET['partie'], $idChemin);
	
	if(isset($_GET['lancerPartie']))
	{	
		
		$carteTiree = NULL;
		$j=0;
		$tabJoueurs[0]= $_GET['pseudo'];
		if(!(tableVide($link, "projet__Dalle"))){
			executeQuery($link, "DELETE FROM projet__Dalle");
		}
		if(!(tableVide($link, "projet__estRealisee"))){
			executeQuery($link, "DELETE FROM projet__estRealisee");
			executeQuery($link, "DELETE FROM projet__Action");
		}	
		if(!(tableVide($link, "projet__Pion"))){
			executeQuery($link, "DELETE FROM projet__Pion");
		}
		$i=0;
		$couleur= $_GET['couleur'];
		foreach($_GET['joueur'] as $valJoueur)
		{
			$query=  "UPDATE projet__Joueur SET couleur = '".$couleur[$i]."' WHERE pseudo= '".$valJoueur."'";
			executeUpdate($link, $query);
			$i++;
			$tabJoueurs[]= $valJoueur;
			ajoutPion($valJoueur, $link);
			
		}
		$query=  "UPDATE projet__Joueur SET couleur = '".$couleur[$i]."' WHERE pseudo= '".$_GET['pseudo']."'";
		executeUpdate($link, $query);
		ajoutPion($_GET['pseudo'], $link);
		if((tableVide($link, "projet__Carte")) && (tableVide($link, "projet__Pioche"))){
			$query = "INSERT INTO projet__Pioche(idPioche) VALUES ('100')";
			executeQuery($link, $query);
			remplirPioche($link);
		} else {
			executeQuery($link, "DELETE FROM projet__Carte");
			$query = "UPDATE projet__Pioche SET idPioche= '100'";
			executeQuery($link, $query);
			remplirPioche($link);
		}	
	}
	
	
	if(isset($_GET['Piocher']))
	{
		foreach($_GET['joueur'] as $valJoueur){
			$tabJoueurs[]= $valJoueur;
		}
		$j= $_GET['joueur_joue'];
		
		
		$donnees= tirageCarte($link);
		$carteTiree= $donnees['type'];
		$fichierCarteTiree= $donnees['image'];
		
		$query= "SELECT probabilite FROM projet__Carte WHERE type= '".$carteTiree."'";
		$result= executeQuery($link, $query);
		$donneesProb = mysqli_fetch_assoc($result);
		$proba= $donneesProb['probabilite']-1;
		$query2 = "UPDATE projet__Carte SET probabilite= '".$proba."' WHERE type= '".$carteTiree."'";
		executeQuery($link, $query2);
		
		$idA = ajoutAction($link, $carteTiree, $tabJoueurs[$j]);
		//$joueur_joue = $tabJoueurs[$j];
		
	}
	
	if((isset($_GET['avancer1'])) || (isset($_GET['avancer2'])) || (isset($_GET['avancer3'])) || (isset($_GET['avancer4'])) || (isset($_GET['activer'])))
	{
		
		foreach($_GET['joueur'] as $valJoueur){
			$tabJoueurs[]= $valJoueur;
		}
		$j= $_GET['joueur_joue'];
		//$joueur_joue = $tabJoueurs[$j];
		
		/*if($j==4){
			$k=$j-1;
		}else {
			$k=$j;
		}
				
		if(joueurPerdu($link, $tabJoueurs[$k])==false){
			$joueur_joue = $tabJoueurs[$k];
		}else {	
			while(joueurPerdu($link, $tabJoueurs[$k])==true){
				$k=$k+1;
				if($k==4){
					$k=0;
				}
			}
			$joueur_joue = $tabJoueurs[$k];
		}*/
		
	} 
	
	if(isset($_GET['activer'])){
		activer_trou($link, $_GET['carteTiree'], $tabJoueurs[$j-1], $_GET['partie']);
		if($j==4){
			$j=0;
		}
	}
	
	if (isset($_GET['avancer1'])){
		avancer($link, 1, $tabJoueurs[$j-1], $_GET['carteTiree'], $_GET['action']);
		if($j==4){
			$j=0;
		}
	}
	
	if (isset($_GET['avancer2'])){ 
		avancer($link, 2, $tabJoueurs[$j-1], $_GET['carteTiree'], $_GET['action']);
		if($j==4){
			$j=0;
		}
	}
	
	if (isset($_GET['avancer3'])){ 
		avancer($link, 3, $tabJoueurs[$j-1], $_GET['carteTiree'], $_GET['action']);
		if($j==4){
			$j=0;
		}
	}
	
	if (isset($_GET['avancer4'])){ 
		avancer($link, 4, $tabJoueurs[$j-1], $_GET['carteTiree'], $_GET['action']);
		if($j==4){
			$j=0;
		}
	}
	
	
	
		
?>

<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Page jeu</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
	<table>
		<tbody>			
			<tr>
				<td><div class="J1">
				<?php 
				echo $tabJoueurs[0];
				$tabPionsJoueur = pionsJoueur($link, $tabJoueurs[0]);
				foreach($tabPionsJoueur as $val){?>
				<img src="<?php echo $val ?>" class="Pion" alt="Pion" />			
				<?php }	?>
			
				</div>
				</td>
				<td>
					<div class= "messages">
				
					</div>
				</td>
				<td>
					<div class= "J2">
					<?php 
					echo $tabJoueurs[1];
					$tabPionsJoueur = pionsJoueur($link, $tabJoueurs[1]);
					foreach($tabPionsJoueur as $val){?>
					<img src="<?php echo $val ?>" class="Pion" alt="Pion" />			
					<?php }	?>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<?php if(!isset($_GET['Piocher'])){?>
					<div class= "pioche">
						<img src="./images/carteDos.png" class="pioche" alt="pioche" />
					</div>
					<?php } else {?>
					<div class= "pioche">
						<img src="<?php echo $fichierCarteTiree; ?>" class="pioche" alt="pioche" />
					</div>
					<?php } ?>
				</td>
				<td>
					<div class= "plateau">
				  		<?php   
							if(recupererModifDalle($link)==false)
							{
								printChemin();
							} else {
								$dalleModif= recupererModifDalle($link);
								foreach($dalleModif as $val){
									list($x,$y)=$chemin[$val];
									$plateau[$x][$y] = TROU;
								}
								printChemin();
							}
						?>
					</div>
				</td>
				<td>
					<div class= "statistiques">
				
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class= "J3">
					<?php 
					echo $tabJoueurs[2];
					$tabPionsJoueur = pionsJoueur($link, $tabJoueurs[2]);
					foreach($tabPionsJoueur as $val){?>
					<img src="<?php echo $val ?>" class="Pion" alt="Pion" />			
					<?php }	?>
					</div>
				</td>
				<td>
					<div class= "commandes">
					<?php if(!isset($_GET['Piocher'])){ ?>
							
								<?php $joueur_joue = $tabJoueurs[$j];
								echo "C'est au tour de ".$joueur_joue; ?>
								<form action = "index.php" method ="GET">
									<input type="hidden" name="page" value="jeu"/>
									<input type="hidden" name="partie" value="<?php echo $_GET['partie']; ?>"/>
									<?php foreach($tabJoueurs as $val){?>
										<input type="hidden" name="joueur[]" value="<?php echo $val;?>" /> <?php } ?>
									<input type="hidden" name="joueur_joue" value="<?php echo $j; ?>"/>
									<input type="hidden" name="carteTiree" value="<?php echo $carteTiree; ?>"/>
									<input type="submit" name="Piocher" value="Piocher">
									<input type="submit" name="Finir" value="Finir Partie">
								</form>
							
					<?php } else {  
							$reponse= typeCarte($link, $carteTiree);
							$carte=mysqli_fetch_assoc($reponse);
							if($carte['typeCarte']== "avance"){ //fonction qui renvoie le type ?>
									
										<?php $joueur_joue = $tabJoueurs[$j];
										echo "C'est au tour de ".$joueur_joue; 
										?>
										<form action = "index.php" method ="GET">
											<input type="hidden" name="page" value="jeu"/>
											<input type="hidden" name="partie" value="<?php echo $_GET['partie']; ?>"/>
											<input type="hidden" name="action" value="<?php echo $idA; ?>"/>
											<?php foreach($tabJoueurs as $val){?>
											<input type="hidden" name="joueur[]" value="<?php echo $val;?>" /> <?php } ?>
											<input type="hidden" name="joueur_joue" value="<?php echo $j+1; ?>"/>
											<input type="hidden" name="carteTiree" value="<?php echo $carteTiree; ?>"/>
											<?php   $tabNbPion = nombre_pion($link, $joueur_joue);
												
												foreach($tabNbPion as $val){ //fonction qui renvoie le nombre de pion restant ?>	
													<input type="submit" name="<?php echo 'avancer'.$val; ?>" value="<?php echo 'Avancer pion '.$val; ?>"> 
												<?php } ?>
										</form> 
											
									
							<?php } else { ?>
									
										<?php $joueur_joue = $tabJoueurs[$j];
										echo "C'est au tour de ".$joueur_joue; 
										?>
										<form action = "index.php" method ="GET">
											<input type="hidden" name="page" value="jeu"/>
											<input type="hidden" name="partie" value="<?php echo $_GET['partie']; ?>"/>
											<input type="hidden" name="action" value="<?php echo $idA; ?>"/>
											<?php foreach($tabJoueurs as $val){?>
											<input type="hidden" name="joueur[]" value="<?php echo $val;?>" /> <?php } ?>
											<input type="hidden" name="joueur_joue" value="<?php echo $j+1; ?>"/>
											<input type="hidden" name="carteTiree" value="<?php echo $carteTiree; ?>"/>
											<input type="submit" name="activer" value="Activer">
										</form>
															
							<?php } ?>
							
					<?php } ?>
					</div>
				</td>
				<td>
					<div class= "J4">
						<?php 
						echo $tabJoueurs[3];
						$tabPionsJoueur = pionsJoueur($link, $tabJoueurs[3]);
						foreach($tabPionsJoueur as $val){?>
							<img src="<?php echo $val ?>" class="Pion" alt="Pion" />			
						<?php }	?>
					</div>
				</td>
			</tr>
				

			
		</tbody>
	</table>


	
</body>
</html>
