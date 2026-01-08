<?php
session_start();

// * Détruire la session utilisateur
session_destroy();

// Redirection vers l'accueil
header('Location: index.php');
exit;
?>