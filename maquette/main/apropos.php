<html>

<head>
	<title> A propos</title>
	<link rel="stylesheet" media="all" type="text/css" href="./css/styles.css">
</head>

<body>
	<div id="regleJeu">
		<h1> Regles du jeu : </h1>
		<p> Le jeu ’Croque Légumes’ est un jeu dans lequel au plus quatre joueurs peuvent jouer. Chaque joueur dispose initialement de 4 pions de même couleur ayant chacun un numéro allant de 1 à 4.L’espace de jeu correspond à un chemin dont chaque cellule correspond à une place possible pour un pion. A l’extrémité du parcours se trouve une salade qui représente l’objectif. Un cellule peut être associée à une dalle sur laquelle le pion peut tenir, ou un trou dans lequel le pion peut tomber. L’état des cellules évolue durant la partie. Le joueur qui atteint la salade le premier a gagné.Un joueur peut avoir plusieurs pions en même temps sur le parcours. Dans l’état initial, le chemin ne comporte aucun trou. </p>
	
	</div>
	<div id="carte">
		<h1> Cartes possibles et leur probabilite : </h1>
			<h2> Carte d'avancement :</h2>
			<p> le joueur peut avancer un de ses pions du nombre de cases indiqué sur la carte. Si la case destination est déjà occupée par un autre pion, le pion sera placé sur la première case libre se trouvant juste devant la case destination. S’il s’agit d’un trou, le pion tombe alors dans le trou. A noter que pour les déplacements de 2 ou 3 cases, si un trou est franchi lors du déplacement, le saut au-dessus du trou compte pour une cas.</p>
			<h2>Carte légume : </h2>
				<ul>
					<li>Carte carotte : Deux trous apparaissent aléatoirement sur le parcours.</li>
					<li>Carte chou :Deux trous apparaissent sous les pions des adversaires choisis aléatoirement.</li>
					<li>Carte poireau :Deux trous apparaissent sous les pions de tous les adversaires.</li>
				</ul>
			<h2> Probabilités : </h2>
				<ul>
					<li>un joueur a 20 chances sur 100 de tirer une carte d'avancement d’une case.</li>
					<li>un joueur a 20 chances sur 100 de tirer une carte d'avancement de deux cases.</li>
					<li>un joueur a 20 chances sur 100 de tirer une carte d'avancement de trois cases.</li>
					<li>un joueur a 25 chances sur 100 de tirer une carte 'carotte'.</li>
					<li>un joueur a 10 chances sur 100 de tirer une carte 'chou'.</li>
					<li>un joueur a 5 chances sur 100 de tirer une carte 'poireau'.</li>
				</ul>
	</div>
	
</body>
</html>

