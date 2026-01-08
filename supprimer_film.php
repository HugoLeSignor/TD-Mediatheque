<?php
require_once 'config.php';
session_start();

// ! Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

// * Récupérer l'ID du film
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: films.php');
    exit;
}

$film_id = intval($_GET['id']);

// Récupérer le film
$sql = "SELECT * FROM fiche_film WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(':id' => $film_id));
$film = $stmt->fetch();

if (!$film) {
    die("Film non trouvé.");
}

// Vérifier que l'utilisateur est le créateur du film
if ($film['user_id'] != $_SESSION['user_id']) {
    die("Vous n'êtes pas autorisé à supprimer ce film.");
}

// Supprimer l'image si elle existe
if ($film['image'] && file_exists('uploads/' . $film['image'])) {
    unlink('uploads/' . $film['image']);
}

// Supprimer le film de la base de données
$sql = "DELETE FROM fiche_film WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(':id' => $film_id));

// Rediriger vers la liste des films
header('Location: films.php');
exit;
?>