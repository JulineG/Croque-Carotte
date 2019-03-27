<?php
	// FONCTION POUR CONNEXION REQUETE 
	
	$dbHost = "localhost";// 
	$dbUser = "p1609223";// 
	$dbPwd = "zlS4Nyn6";// 
	$dbName = "p1609223";
	$link = getConnection($dbHost, $dbUser, $dbPwd, $dbName);

	define('RIEN', 'vide');
	define('DALLE', 'dalle');
	define('FIN', 'carotte');
	define('TROU', 'trou');

	$dim= 5;
	$numCase= 0;
	$plateau= array();
	$chemin= array();

	/*Cette fonction prend en entrée l'identifiant de la machine hôte de la base de données, les identifiants (login, mot de passe) d'un utilisateur autorisé 
	sur la base de données contenant les tables pour le chat et renvoie une connexion active sur cette base de donnée. Sinon, un message d'erreur est affiché.*/
function getConnection($dbHost, $dbUser, $dbPwd, $dbName)
	{
		$link = mysqli_connect($dbHost, $dbUser, $dbPwd, $dbName);
	
		if(mysqli_connect_errno())
		{
			printf("Echec de connexion : %s", mysqli_connect_error());
		}
		else
			return $link;
	}

	/*Cette fonction prend en entrée une connexion vers la base de données du chat ainsi 
	qu'une requête SQL SELECT et renvoie les résultats de la requête. Si le résultat est faux, un message d'erreur est affiché*/
function executeQuery($link, $query)
	{
		$result = mysqli_query($link, $query);
		if($result == FALSE)
		{	
			printf("Echec de la requête1");			
		}
		else {
			return $result;
		}
	}

	/*Cette fonction prend en entrée une connexion vers la base de données du chat ainsi 
	qu'une requête SQL INSERT/UPDATE/DELETE et ne renvoie rien si la mise à jour a fonctionné, sinon un 
	message d'erreur est affiché.*/
function executeUpdate($link, $query)
	{
		$result = mysqli_query($link, $query);
		if($result == FALSE)
		{
			printf("Echec de la requête");
		}
	}

	/*Cette fonction ferme la connexion active $link passée en entrée*/
function closeConnexion($link)
	{
		mysql_close($link);
	}
	
	//FONCTION POUR L'UTILISATEUR 
	
	/*Cette fonction prend en entrée un pseudo à ajouter à la relation utilisateur et une connexion et 
	retourne vrai si le pseudo est disponible (pas d'occurence dans les données existantes), faux sinon*/
function checkAvailability($pseudo, $link)
	{
		$query = "SELECT * FROM projet__Joueur WHERE pseudo = '".$pseudo."';";
		$result = executeQuery($link, $query);
		return mysqli_num_rows($result)==0 ;
	}

	/*Cette fonction prend en entrée un pseudo et un mot de passe, associe une couleur aléatoire dans le tableau de taille fixe  
	array('red', 'green', 'blue', 'black', 'yellow', 'orange') et enregistre le nouvel utilisateur dans la relation utilisateur via la connexion*/
function register($pseudo, $nom, $prenom, $hashPwd, $link)
{
	//$tabCouleurs = array('rouge', 'violet', 'bleu', 'jaune');
	//$couleur = $tabCouleurs[rand(0,3)];
	$query = "INSERT INTO projet__Joueur(pseudo, nom, prenom, couleur, hachageMdp, etat, partieGagne) VALUES ('".$pseudo."','".$nom."', '".$prenom."', NULL , '".$hashPwd."','disconnected', '0')";
	executeUpdate($link, $query);
}

	/*Cette fonction prend en entrée un pseudo d'utilisateur et change son état en 'connected' dans la relation 
	utilisateur via la connexion*/
function setConnected($pseudo, $link)
{
	$query = "UPDATE projet__Joueur SET etat = 'connected' WHERE pseudo = '".$pseudo."';";
	executeUpdate($link, $query);
}

function setDeconnected($pseudo, $link)
{
	$query = "UPDATE projet__Joueur SET etat = 'deconnected' WHERE pseudo = '".$pseudo."';";
	executeUpdate($link, $query);
}

	/*Cette fonction prend en entrée un pseudo et mot de passe et renvoie vrai si l'utilisateur existe (au moins un tuple dans le résultat), faux sinon*/
function getUser($pseudo, $hashPwd, $link)
{
	$query = "SELECT * FROM projet__Joueur WHERE pseudo = '".$pseudo."' AND hachageMdp = '".$hashPwd."';";
	$result = executeQuery($link, $query);
	return mysqli_fetch_row($result); 	
}

function nbUser($link)
{
	$query = "SELECT COUNT(pseudo) AS nbUser FROM projet__Joueur ;";
	return mysqli_query($link, $query);
}

function nbParties($link)
{
	$query = "SELECT COUNT(idP) AS nbParties FROM projet__Partie WHERE dateCreation >= SUBDATE(NOW(), INTERVAL 31 DAY);";
	return mysqli_query($link, $query);
}

function nbPartiesJoueur($link, $pseudo)
{
	$query = "SELECT COUNT(P.idP) AS nbPartiesJoueur FROM projet__Partie P, projet__Joue J, projet__Joueur J1 WHERE J1.pseudo = '".$pseudo."' AND J1.pseudo=J.pseudo AND J.idP = P.idP AND  P.dateCreation >= SUBDATE(NOW(), INTERVAL 31 DAY);";
	return mysqli_query($link, $query);
}

 /* Renvoie NULL si le joueur n'est pas connecté */
function checkConnection($link, $pseudo)
{
	$query = "SELECT * FROM projet__Joueur WHERE pseudo= '".$pseudo."' AND etat= 'connected'";
	$result = executeQuery($link, $query);
	return mysqli_fetch_row($result); 
}

function dernierePartie($link, $pseudo)
{
	$query = "SELECT P.* FROM projet__Partie P, projet__Joue J WHERE J.pseudo='".$pseudo."' AND P.idP=J.idP AND NOT EXISTS(SELECT P1.dateCreation FROM projet__Partie P1 WHERE P1.dateCreation > P.dateCreation)";
	
	return executeQuery($link, $query);
}

function joueursInscrit($link)
{
	$query = "SELECT pseudo FROM projet__Joueur";
	$result = executeQuery($link, $query);
	$tabJoueurs = array();
	while($r = mysqli_fetch_array($result)){
		$tabJoueurs[]= $r['pseudo'];
	}
	return $tabJoueurs;
}

function creerPartie($initiateur, $link)
{
	$query1 = "SELECT NOW() AS date";
	$result1 = executeQuery($link, $query1);
	$date=mysqli_fetch_assoc($result1);

	$query = "INSERT INTO projet__Partie(nom,etat, dateCreation, initiateur) VALUES ('NULL', 'En cours', '".$date['date']."' , '".$initiateur."')";
	executeUpdate($link, $query);
	
	$queryidP = "SELECT P.* FROM projet__Partie P WHERE NOT EXISTS(SELECT P1.idP FROM projet__Partie P1 WHERE P1.idP>P.idP)";
	$resultidP= executeQuery($link, $queryidP);
	$idP=mysqli_fetch_assoc($resultidP);


	$query2 = "SELECT CONCAT('Partie' , '".$idP['idP']."') AS nom FROM projet__Partie P";
	$result2 = executeQuery($link, $query2);
	$nom=mysqli_fetch_assoc($result2);
	$query3= "UPDATE projet__Partie SET nom = '".$nom['nom']."' WHERE nom='NULL'";
	executeUpdate($link, $query3);

	$query = "INSERT INTO projet__Joue(idP, pseudo) VALUES ('".$idP['idP']."', '".$initiateur."')";
	executeUpdate($link, $query);
	
	return $idP['idP'];
	
}

function ajoutJoueurAPartie($joueur, $link)
{
	$queryidP = "SELECT P.* FROM projet__Partie P WHERE NOT EXISTS(SELECT P1.idP FROM projet__Partie P1 WHERE P1.idP>P.idP)";
	$resultidP= executeQuery($link, $queryidP);
	$idP=mysqli_fetch_assoc($resultidP);
	$query = "INSERT INTO projet__Joue(idP, pseudo) VALUES ('".$idP['idP']."', '".$joueur."')";
	executeUpdate($link, $query);
}

function ajoutPion($joueur,$link)
{
	for($i=1;$i<=4;$i++)
	{
		$query1= "SELECT couleur FROM projet__Joueur WHERE pseudo= '".$joueur."'";
		$result1= executeQuery($link, $query1);
		$couleur= mysqli_fetch_assoc($result1);
		$query2 = "SELECT CONCAT('".$couleur['couleur']."' , '".$i."') AS codePion";
		$result2 = executeQuery($link, $query2);
		$codePion=mysqli_fetch_assoc($result2);
		$fichier = "images/".$codePion['codePion'].".png";
	
		$query = "INSERT INTO projet__Pion(codePion, fichier, etat, position, idA, pseudo) VALUES ('".$codePion['codePion']."', '".$fichier."', 'passif', NULL, NULL, '".$joueur."')";
		executeUpdate($link, $query);
	}
}

function pionsJoueur($link, $pseudo)
{
	$query = "SELECT P.fichier FROM projet__Pion P WHERE pseudo = '".$pseudo."' AND P.etat = 'passif' ";
	$result = executeQuery($link, $query);
	$tabPionsJoueur = array();
	while($r = mysqli_fetch_array($result)){
		$tabPionsJoueur[]= $r['fichier'];
	}
	return $tabPionsJoueur;
}

function nombre_pion($link, $pseudo)
{
	$query = "SELECT SUBSTR(P.codePion,-1) AS nbPion FROM projet__Pion P WHERE P.pseudo='".$pseudo."' AND P.etat != 'perdu'";
	$result= executeQuery($link, $query);
	$tabNbPion = array();
	while($r = mysqli_fetch_array($result)){
		$tabNbPion[]= $r['nbPion'];
	}
	return $tabNbPion;
}

function typeCarte($link, $carte) 
{
	$query = "SELECT SUBSTR(c.type,1,6) AS typeCarte FROM projet__Carte c WHERE c.type= '".$carte."'";
	return executeQuery($link, $query);
}

function remplirPioche($link)
{	
	
	$carte = array('avance1'=>20, 'avance2'=>20, 'avance3'=>20, 'carteCarotte'=>25, 'carteChou'=>10, 'cartePoireau'=>5);
	
	foreach($carte as $val => $prob){
		$img= "./images/".$val.".png";
		$query1 = "INSERT INTO projet__Carte(type, image, idPioche, probabilite) VALUES ('".$val."', '".$img."', '100', '".$prob."')";
		executeQuery($link, $query1);
	}
	
}

function initPlateau()
{
	global $plateau, $dim;
	$i; $j;
	$tmp= array();
	for($i=0; $i<$dim; $i++){
		for($j=0; $j<$dim; $j++){
			$tmp[$j]=RIEN;
		}
		$plateau[$i]=$tmp;
	}
}

function estLibre ($x, $y)
{
	global $plateau, $dim;
	if(($x>=$dim) || ($y>=$dim)|| ($x<0) || ($y<0))
	{
		return false;
	}
	else {
		return ($plateau[$x][$y] == RIEN);
	}
}

function estAuBord($x, $y, $sens)
{
	global $plateau, $dim;
	switch($sens)
	{
		case "est" : return ($y==$dim-1);
		case "sud" : return ($x==$dim-1);
		case "ouest" : return ($y==0);
		case "nord" : return ($x==0);
	}
}

function avancerEstPossible($x, $y, $sens) 
{
	switch ($sens)
	{
		case "est" : return ((estLibre($x, $y+1)) && ((estLibre($x, $y+2)) || (estAuBord($x,$y+1, "est"))));
		case "sud" : return ((estLibre($x+1, $y)) && ((estLibre($x+2, $y)) || (estAuBord($x+1,$y, "sud"))));
		case "ouest" : return ((estLibre($x, $y-1)) && ((estLibre($x, $y-2)) || (estAuBord($x,$y-1, "ouest"))));
		case "nord" : return ((estLibre($x-1, $y)) && ((estLibre($x-2, $y)) || (estAuBord($x-1,$y, "nord"))));
	}	
}

function avance($x,$y,$sens)
{
	switch($sens)
	{
		case "est" : return array('x' => $x, 'y'=>$y+1);
		case "sud" : return array('x' => $x+1, 'y'=>$y);	
		case "ouest" : return array('x' => $x, 'y'=>$y-1);
		case "nord" : return array('x' => $x-1, 'y'=>$y);
	}
}

function sensSuivant($sens)
{
	switch($sens)
	{
		case "est": return ("sud");
		case "sud": return ("ouest");
		case "ouest": return ("nord");
		case "nord": return ("est");
	}
}	

function peutTournerTribord($x,$y,$sens)
{
	switch($sens)
	{
		case "est": return((estLibre($x+1,$y)) && (estLibre($x+2,$y)));
		case "sud": return((estLibre($x,$y-1)) && (estLibre($x,$y-2)));
		case "ouest": return((estLibre($x-1,$y)) && (estLibre($x-2,$y)));
		case "nord": return((estLibre($x,$y+1)) && (estLibre($x,$y+2)));
	}
}

function affecte($x,$y,$val)
{
	global $chemin, $plateau, $numCase;
	if ($val== DALLE)
	{
		$chemin[$numCase]=array($x,$y);
		$plateau[$x][$y] = $numCase;
		$numCase++;
	}
	else
	{
		$plateau[$x][$y] = $val;
	}
}

function chemine($x,$y,$sens)
{	
	
	while(avancerEstPossible($x,$y,$sens))
	{
		affecte($x,$y,DALLE);
		$caseSuivante = avance($x,$y,$sens);
		$x = $caseSuivante['x'];
		$y = $caseSuivante['y'];
	}
	if (peutTournerTribord($x,$y,$sens))
	{
		$sens = sensSuivant($sens);
		chemine($x,$y,$sens);
	}
	else
	{
		affecte($x,$y,FIN);
	}
}

function printChemin(){
	global $plateau;
	global $dim, $link;

	echo "<table>";
	for($i=0; $i<$dim; $i++){
		echo "<tr>";
		for($j=0; $j<$dim; $j++){
			$val=$plateau[$i][$j];
			if(getType($val) == "integer"){
				
				echo "<td id='".$val."' class='".DALLE."'>";
				if(fichierPionActif($link, $val) != false){
					$donnees= fichierPionActif($link, $val);
					echo "<img src='"; echo $donnees['fichier']; echo "' class='Pion' alt='Pion'/>";
				} else {
					echo $val;
				}
				echo "</td>";
			}else{
				
				echo "<td class='".$val."'> </td>";
			}
		}
		echo "</tr>";
	}
	echo "</table>";
}

function placeTrou($case, $idP)
{
	global $chemin, $link;
	global $plateau;
	list($x,$y)=$chemin[$case-1];
	$plateau[$x][$y] = TROU;
	ajoutDalle($link, $case-1, $idP);
}

function activer_trou($link, $carte, $pseudo, $idP)
{	
	
	$carte = substr($carte, 5);
	echo $carte;
	if (strcmp($carte , "Carotte")==0) // Partie ou des trous apparaissent aléatoirement ATTENTION : il manque le teste des pions sur la position trou juste récupérer les deux variables aléatoire et utilisé les !
	{
		$i = rand(1, 16);
		placeTrou($i, $idP);

		do
		{
			$j = rand(1, 16);
			placeTrou($j, $idP);
		} while ($i == $j);
	}
	elseif (strcmp($carte, "Chou")==0) // partie qui permet de "tuer deux pions adverses"
	{
		$query1 = "SELECT J.couleur FROM projet__Joueur J WHERE J.pseudo = '".$pseudo."' ";
		echo $query1;
		$resultCouleur = executeQuery($link, $query1);
		$query2 = "SELECT P.* FROM projet__Pion P WHERE P.pseudo != '".$pseudo."' AND P.etat = 'actif'";
		echo $query2;
		$reponse2= executeQuery($link, $query2); 
		
		$tabPionsActif= array();
		while($r = mysqli_fetch_array($reponse2)){
			$tabPionsActif[]= $r['codePion'];
		}
		
		if(count($tabPionsActif)==1)
		{
			$pion_mort= $tabPionsActif[0];
			
			$query3 = "SELECT P.* FROM projet__Pion P WHERE P.codePion = '".$pion_mort."'";
			$result3 = executeQuery($link,$query3);
			$tab= mysqli_fetch_assoc($result3);
			placeTrou($tab['position']+1, $idP);
			$query4 = "UPDATE projet__Pion P SET P.etat = 'perdu' WHERE P.codePion = '".$pion_mort."' ";
			executeQuery($link,$query4);
		} elseif(count($tabPionsActif)!=0) {
			
			
			$limite = count($tabPionsActif);
			$query5 = "SELECT DISTINCT P.* FROM projet__Pion P WHERE P.pseudo != '".$pseudo."' AND P.etat = 'actif' ORDER BY RAND() LIMIT ".$limite."";
			echo $query5;
			$result5 = executeQuery($link, $query5);
			$donnees = mysqli_fetch_assoc($result5);
			$pion_mort1= $donnees['codePion'];

			$limite = count($tabPionsActif)-1;
			$query5bis = "SELECT DISTINCT P.* FROM projet__Pion P WHERE P.codePion!= '".$pion_mort1."' AND P.pseudo != '".$pseudo."' AND P.etat = 'actif' ORDER BY RAND() LIMIT ".$limite."";
			echo $query5bis;
			$result5bis = executeQuery($link, $query5bis);
			$donnees = mysqli_fetch_assoc($result5bis);
			$pion_mort2= $donnees['codePion'];

			$query6 = "SELECT P.position FROM projet__Pion P WHERE P.codePion = '".$pion_mort1."' OR P.codePion = '".$pion_mort2."' ";
			echo $query6;
			$result6 = executeQuery($link,$query6);
			$tabPionsMort = array();
			while($r = mysqli_fetch_array($result6)){
				$tabPionsMort[]= $r['position'];
			}
			placeTrou($tabPionsMort[0]+1, $idP);
			placeTrou($tabPionsMort[1]+1, $idP);
			$query7 = "UPDATE projet__Pion P SET P.etat = 'perdu' WHERE P.codePion = '".$pion_mort1."' ";
			echo $query7;
			$query8 = "UPDATE projet__Pion P SET P.etat = 'perdu' WHERE P.codePion = '".$pion_mort2."' ";
			echo $query8;
			executeQuery($link,$query7);
			executeQuery($link,$query8);
		}
	}
	else // Partie qui permet de tuer tous les pions sur le plateau 
	{
		$query1 = "SELECT P.position FROM projet__Pion P WHERE P.pseudo != '".$pseudo."' AND P.etat= 'actif' ";
		echo $query1;
		$result = executeQuery($link,$query1);
		if(!empty($result)){
			$tabPionsMort = array();
			while($r = mysqli_fetch_array($result)){
				$tabPionsMort[]= $r['position'];
			}
			foreach ($tabPionsMort as $value)
			{
				placeTrou($value+1, $idP);
			}
			$query2 = "UPDATE projet__Pion P SET P.etat = 'perdu' WHERE P.etat = 'actif' AND P.pseudo != '".$pseudo."' ";
			executeQuery($link,$query2);
		}
	}
	
}

//Renvoie true si la table est vide
function tableVide($link, $table)
{
		$query = "SELECT * FROM ".$table." ";
		$result = executeQuery($link, $query);
		return empty($result);
} 

function tirageCarte($link)
{
	$query1 = "SELECT * FROM projet__Pioche";
	$resultidPioche= executeQuery($link, $query1);
	$idPioche=mysqli_fetch_assoc($resultidPioche);
	$query2 = "SELECT DISTINCT * FROM projet__Carte WHERE probabilite != '0' ORDER BY RAND() LIMIT ".$idPioche['idPioche']."";
	$result = executeQuery($link, $query2);
	
	
	$idPioche = $idPioche['idPioche']-1;
	$query3 = "UPDATE projet__Pioche SET idPioche= '".$idPioche."'";
	executeQuery($link, $query3);
	
	return mysqli_fetch_assoc($result);
}

function avancer($link, $nm_pion, $pseudo, $carte, $idA)	//fonction qui permet de faire avancer les pions d'un joueur en fonction de la carte
{
	
	
	$query1 = "SELECT J.couleur FROM projet__Joueur J WHERE J.pseudo = '".$pseudo."' ";
	$resultCouleur = executeQuery($link, $query1);
	$couleur=mysqli_fetch_assoc($resultCouleur);
	$query2 = "SELECT P.position FROM projet__Pion P, projet__Joueur J WHERE P.pseudo = '".$pseudo."' AND J.pseudo = '".$pseudo."' AND P.codePion = '".$couleur['couleur']."".$nm_pion."' ";
	$resultPion = executeQuery ($link, $query2);
	$position_pion=mysqli_fetch_assoc($resultPion);
	if ($position_pion['position'] == NULL)
	{
		$position_pion = substr($carte, -1);
		
		do{
			$query = "SELECT P.* FROM projet__Pion P WHERE P.position='".$position_pion."' AND P.etat = 'actif' AND EXISTS(SELECT P1.position FROM projet__Pion P1 WHERE P.position=P1.position)";
			$resultPosition = executeQuery ($link, $query);
			$positionOccupee=mysqli_fetch_assoc($resultPosition);
			$position_pion = $position_pion+1;
		}while($positionOccupee != false);
		$position_pion = $position_pion-1;
		$query3 = "UPDATE projet__Pion P SET P.position = '".$position_pion."', P.etat='actif', P.idA='".$idA."' WHERE P.pseudo = '".$pseudo."' AND P.codePion = '".$couleur['couleur']."".$nm_pion."' ";
		echo $query3;
		executeQuery($link, $query3);
	}
	else 
	{	
		$carte = substr($carte, -1);
		$position_pion = $position_pion['position'] + $carte;
		do{
			$query = "SELECT P.* FROM projet__Pion P WHERE P.position='".$position_pion."' AND P.etat = 'actif' AND EXISTS(SELECT P1.position FROM projet__Pion P1 WHERE P.position=P1.position)";
			$resultPosition = executeQuery ($link, $query);
			$positionOccupee=mysqli_fetch_assoc($resultPosition);
			$position_pion = $position_pion+1;
		}while($positionOccupee != false);
		$position_pion = $position_pion-1;
		$query3 = "UPDATE projet__Pion P SET P.position = '".$position_pion."', P.etat='actif', P.idA='".$idA."' WHERE P.pseudo = '".$pseudo."' AND P.codePion = '".$couleur['couleur']."".$nm_pion."' ";
		executeQuery($link, $query3);
	}

	$dalleModif= recupererModifDalle($link);
	foreach($dalleModif as $val){
		if($position_pion==$val){
			$query4 = "UPDATE projet__Pion P SET P.etat = 'perdu' WHERE P.position = '".$position_pion."'";
			executeQuery($link,$query4);
		}		
	}
	
	
}

function creerChemin($link, $idP, $idC)
{
	global $numCase;
	initPlateau();
	affecte(0,0, DALLE);
	chemine(0,0,'est');
	
	$query = "SELECT * FROM projet__Chemin WHERE idP = '".$idP."' ";
	echo $query;
	$result = executeQuery($link, $query);
	$donnees= mysqli_fetch_assoc($result);
	if(empty($donnees)){
		$query1 = "INSERT INTO projet__Chemin (idChemin, taille, idP) VALUES ('".$idC."', '".$numCase."', '".$idP."')";
		echo $query1;
		executeQuery($link, $query1);
	}
}

function ajoutAction($link, $carte, $pseudo)
{
	$query = "INSERT INTO projet__Action(carte) VALUES ('".$carte."')";
	executeQuery($link, $query);
	$queryidA = "SELECT A.* FROM projet__Action A WHERE NOT EXISTS(SELECT A1.idA FROM projet__Action A1 WHERE A1.idA>A.idA)";
	$resultidA= executeQuery($link, $queryidA);
	$idA=mysqli_fetch_assoc($resultidA);
	$query = "INSERT INTO projet__estRealisee(pseudo, idA) VALUES ('".$pseudo."', '".$idA['idA']."')";
	executeQuery($link, $query);
	return $idA['idA'];
}

function ajoutDalle($link, $numCase, $idP)// Ajoute dans la base de donnée les dalles modifiées par une action
{
	$queryidChemin = "SELECT idChemin FROM projet__Chemin  WHERE idP= '".$idP."'";
	$resultidChemin= executeQuery($link, $queryidChemin);
	$idChemin=mysqli_fetch_assoc($resultidChemin);
	$queryidA = "SELECT A.* FROM projet__Action A WHERE NOT EXISTS(SELECT A1.idA FROM projet__Action A1 WHERE A1.idA>A.idA)";
	$resultidA= executeQuery($link, $queryidA);
	$idA=mysqli_fetch_assoc($resultidA);
	$query = "INSERT INTO projet__Dalle (numCase, idChemin, idA) VALUES ('".$numCase."', '".$idChemin['idChemin']."' , '".$idA['idA']."')";
	executeQuery($link, $query);
}

function recupererModifDalle($link)// Renvoie faux si la table est vide et renvoie un tableau avec le numéro de case des dalles modifiées par la dernière action sinon.
{
	if(tableVide($link, "projet__Dalle")){
		return false;
	} else {
		$query= "SELECT * FROM projet__Dalle D WHERE NOT EXISTS(SELECT D1.idA FROM projet__Dalle D1 WHERE D1.idA>D.idA)";
		$reponse= executeQuery($link, $query); 
		$tabNumCase = array();
		while($r = mysqli_fetch_array($reponse)){
			$tabNumCase[]= $r['numCase'];
		}
		return $tabNumCase;
	}
}

function fichierPionActif($link, $val) //retourne un tableau avec le pion actif à la position $val et retourne false si il n'y a pas de pion
{
	$query = "SELECT * FROM projet__Pion P WHERE P.position='".$val."' AND P.etat = 'actif' ";
	$result = executeQuery($link, $query);
	return mysqli_fetch_assoc($result);
}
	
function joueurPerdu($link, $pseudo) // Retourne true si le joueur a perdu tous ses pions et false sinon
{
	$query = "SELECT * FROM projet__Pion P WHERE P.pseudo='".$pseudo."' AND P.etat = 'perdu' ";
	echo $query;
	$result = executeQuery($link, $query);
	$tabPionsPerdus = array();
	while($r = mysqli_fetch_array($result)){
		$tabPionsPerdus[]= $r['codePion'];
	}
	echo count($tabPionsPerdus);
	if(count($tabPionsPerdus)==4){
		return true;
	} else {
		return false;
	}
}
?>
