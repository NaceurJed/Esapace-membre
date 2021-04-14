<?php
//Pour la deconnexion on va devoir detruire les SESSIONs
//Mais avant il faut les initialiser
session_start(); //initialise la session
session_unset(); //desactive la session
session_destroy(); // detruire la session (pour être sûr)

//Destruction des cookies lors de la deconnexion:
setcookie('log', '', time()-3444, '/', null, false, true); // on ne lui donne pas de contenu et pour le timeon fait une soustraction pour detruire le cookie

header('location: http://localhost/Section%2017-%20Projet%234%20un%20espace%20membre/index.php'); // et on redirige l'utilisateur vers l'accueil non connecté

