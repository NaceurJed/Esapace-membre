<?php
session_start(); //le mettre tout en haut si on veut utiliser des sessions

if (isset($_SESSION['connect'])) { //si la session connect existe (signifie que l'utilisateur est connecté)
	header('location: http://localhost/Section%2017-%20Projet%234%20un%20espace%20membre/index.php'); //on va le diriger vers la page d'accueil, on ne veut pas que notre utilisateur puisse se reconnecter alors qu'il vient de se connecter, 
	//Donc si on est connecté on ne va plus pouvoir aller à la page de connection
}

require('src/connexion.php');

if (!empty($_POST['email']) && !empty($_POST['password'])) {
	//VARIABLES
	$email 		= $_POST['email'];
	$password 	= $_POST['password'];

	//CRYPTER LE PASSWORD
	$password = "aq1".sha1($password."1254")."25";

	//VERIFICATION DE L'ADRESSE MAIL ET DU MOT DE PASSE
	$req = $db->prepare('SELECT * FROM users WHERE email = ?');
	$req->execute(array($email));

	while ($users = $req->fetch()) {
		if($password == $users['password']){
			$error = 0;
			// Création des session pour prendre des info et les afficher en fonction de notre utilisateur comme son pseudo:
			$_SESSION['connect'] = 1; //on va créer une variable session qui s'appelera 'connect' et qui sera égale à 1 (comee ça on va savoir sur tout le site que notre utilisateur est connecté, on ne lui proposera pas de se connecter ni de s'inscrir)
			$_SESSION['pseudo'] = $users['pseudo']; //c'est pour ça qu'on récupérer tout champ de la table dans la requéte sql plus haut (on va stocker le pseudo de l'utilsateur pour pouvoir l'utiliser n'importe quand)

			//On va faire ici la connexion automatique
			if (isset($_POST['conect'])) { //conect c'est ne name de la chekbox (bouton de connextion automatique)
				setcookie('log', $users['secret'], time() + 365*24*3600, '/', null, false, true);
			}

			header('location: http://localhost/Section%2017-%20Projet%234%20un%20espace%20membre/connexion.php?success=1');
				
		}	
	}	
		if ($error = 1) {
			header('location: http://localhost/Section%2017-%20Projet%234%20un%20espace%20membre/connexion.php?error=1');
		}
				
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">		
	<title>Connexion</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
</head>
<body>
	<header>
		<h1>Connexion</h1>
	</header>

	<div class="container">
		<p id="info">Bienvenue sur mon site, Si vous n'êtes pas inscrit, <a href="http://localhost/Section%2017-%20Projet%234%20un%20espace%20membre/">inscrivez-vous</a></p>
		
		<!-- GESTION DES MESSAGES D'ERREUR -->
		<?php

		if (isset($_GET['error'])) {
				echo '<p id="error">Nous ne pouvons pas vous identifier</p>';
			}
		elseif (isset($_GET['success'])) {
			echo '<p id="success">Vous êtes maintenant connecté</p>';
		}
		?>

		<!-- FORMULAIRE -->
		<div id="form">
			<form method="post" action="connexion.php">
				<table>

					<tr>
						<td>Email</td>
						<td><input type="email" name="email" placeholder="example@google.com" required></td>
					</tr>
					<tr>
						<td>Mot de passe</td>
						<td><input type="password" name="password" placeholder="**********" required></td>
					</tr>

				</table>
				<div id="button">
					<button>Connexion</button>	
				</div>
				
				<!-- Connexion automatique-->
				<p>
					<label><input type="checkbox" name="conect" checked>Connexion automatique</label> <!-- le label permet de cocher en cliquant sur le texte-->
				</p>

			</form>
		</div>
	</div>


</body>
</html>