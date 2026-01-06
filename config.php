<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'mediatheque';
$user = 'root';
$pass = '';

// On se connecte à la base de données
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>