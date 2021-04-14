<?php

session_start(); //on verifie si on est déjà connecté pour ne plus afficher le message de demande de connection

require('src/connexion.php'); // on va créer un dossier src et un fichier connexion.php pour se connecter à la base de donner grace à fichier
// require('src/connexion.php'); est equivalent à $db = new PDO('mysql:host=localhost;port=3308;dbname=formation_members;charset=utf8', 'root', '');

/********** INSCRIPTION ***********/
//on commence par controler l'existance des valeurs (pseudo, email, password...)
if (!empty($_POST['pseudo']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password_confirm'])) { //!empty = si pas vide
	
	//on met tous dans des variables:
	$pseudo 			= $_POST['pseudo'];
	$email 				= $_POST['email'];
	$password 			= $_POST['password'];
	$password_confirm 	= $_POST['password_confirm'];

	//Test si password != password_confirm
	if ($password != $password_confirm) {
		header('location: http://localhost/Section%2017-%20Projet%234%20un%20espace%20membre/index.php?error=1&pass=1'); //location pour rediriger l'utilisateur à la page precisée, le message d'erreur sera afficher juste au dessus du formulaire (voir en bas pour le message d'erreur ligne 51)
	}

	//TEST si pseudo déjà utilisé
	$req = $db->prepare('SELECT COUNT(*) AS numberPseudo FROM users WHERE pseudo = ?');
	$req->execute(array($pseudo));
	while ($pseudo_verification = $req->fetch()) { // while: pour parcourir tous les éléments de notre requete, $req contient tous les elements de notre requete, et Fetch va mettre un element par un element à la fois dans $req
		if ($pseudo_verification['numberPseudo'] != 0) { 
			header('location: http://localhost/Section%2017-%20Projet%234%20un%20espace%20membre/index.php?error=1&pseudo=1'); //message d'erreur juste avant le formulaire  
			exit();
		}
	}

	//TEST SI email déjà utilisé
	$req = $db->prepare('SELECT COUNT(*) AS numberEmail FROM users WHERE email = ?');
	$req->execute(array($email));
	while ($email_verification = $req->fetch()) { // while: pour parcourir tous les éléments de notre requete, $req contient tous les elements de notre requete, et Fetch va mettre un element par un element à la fois dans $req
		if ($email_verification['numberEmail'] != 0) { 
			header('location: http://localhost/Section%2017-%20Projet%234%20un%20espace%20membre/index.php?error=1&email=1'); //message d'erreur juste avant le formulaire 
			exit();
		}
	}


	/*
	// TEST de validité de l'adresse Email
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		header('location: http://localhost/Section%2017-%20Projet%234%20un%20espace%20membre/index.php?error=1&emailInvalid=1'); //message d'erreur juste avant le formulaire  
			exit();
	}
	*/

	//HASH (gestion du champ secret de notre table)
	$secret = sha1($email).time();
	$secret = sha1($email).time().time();

	//CRYPTAGE DU PASSWORD
	$password = "aq1".sha1($password."1254")."25";

	// ENVOI DE LA REQUETE DANS LA BASE DE DONNEE
	$req =$db->prepare('INSERT INTO users(pseudo, email, password, secret) VALUES (?, ?, ?, ?)');
	$req->execute(array($pseudo, $email, $password, $secret));
	header('location: http://localhost/Section%2017-%20Projet%234%20un%20espace%20membre/index.php?success=1');
}



?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">		
	<title>Espace membre</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
</head>
<body>
	<header>
		<h1>Inscription</h1>
	</header>

	<div class="container">

		<?php
		if (!isset($_SESSION['connect'])) {?> <!--ici on va demander, si la session n'existe pas, de nous afficher tout le bloc en dessous-->
			

			<p id="info">Bienvenue sur mon site, pour en voir plus, inscrivez-vous. Déjà inscrit, <a href="connexion.php">connectez-vous</a></p>
		

			<!-- GESTION DES MESSAGES D'ERREUR -->
			<?php
			if (isset($_GET['error'])) { 
				if (isset($_GET['pass'])) {
					echo '<p id="error">Les mots de passe ne sont pas identiques !</p>';
				}

				if (isset($_GET['email'])) {
					echo '<p id="error">Cette adresse Email existe déjà.</p>';
				}

				if (isset($_GET['pseudo'])) {
					echo '<p id="error">Ce pseudo est déjà utilisé.</p>';
				}

				if (isset($_GET['emailInvalid'])) {
					echo '<p id="error">Adresse Email non valide.</p>';
				}
			}
			elseif (isset($_GET['success'])) {
				echo '<p id="success">Inscription confirmée.</p>';

			}
			?>



			<!-- FORMULAIRE -->
			<div id="form">
				<form method="post" action="index.php">
					<table>
						<tr>
							<td>Pseudo</td>
							<td><input type="text" name="pseudo" placeholder="Ex: Naceur" required></td> <!-- required= champ obligatoire -->
						</tr>
						<tr>
							<td>Email</td>
							<td><input type="email" name="email" placeholder="example@google.com" required></td>
						</tr>
						<tr>
							<td>Mot de passe</td>
							<td><input type="password" name="password" placeholder="**********" required></td>
						</tr>
						<tr>
							<td>Confirmation du mot de passe </td>
							<td><input type="password" name="password_confirm" placeholder="**********" required></td>
						</tr>

					</table>

					<!-- Connexion automatique-->
					<p>
						<label><input type="checkbox" name="conect" checked>Connexion automatique</label> <!-- le label permet de cocher en cliquant sur le texte-->
					</p>

					<div id="button">
						<button>Inscription</button>	
					</div>

				</form>
			</div>
		<?php } //vu que ce bloc est dans la condition de "if (!isset($_SESSION['connect']))" on ne verra pas le formulaire d'inscription si on est	déjà connecté 
		else{ ?> 
			<hr /> <!-- c'est la ligne qui s'agrandi -->
			<p id="bonjour">
				Bienvenue <?= ucwords($_SESSION['pseudo']) ?><br>  <!-- ici c'est comme un echo $_SESSION['pseudo'] mais en version php7-->
			</p>
			<p id="deconect">
				<a href="deconnexion.php">Déconnexion</a>
			</p>
			

		<?php } ?>

	</div>

</body>
</html>