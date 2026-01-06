<?php
require_once 'config.php';
require_once 'functions.php';
session_start();

// Récupérer les 3 derniers films
try {
    $stmt = $pdo->query("SELECT * FROM fiche_film ORDER BY id DESC LIMIT 3");
    $films = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erreur lors de la récupération des films : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Médiathèque</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Médiathèque</h1>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="user-info">
            <strong>Bonjour <?php echo htmlspecialchars($_SESSION['user_nom']); ?>
                <?php echo htmlspecialchars($_SESSION['user_prenom']); ?></strong>
        </div>
    <?php endif; ?>

    <div class="nav-links">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="deconnexion.php">Se déconnecter</a>
        <?php else: ?>
            <a href="connexion.php">Se connecter</a>
            <a href="inscription.php">S'inscrire</a>
        <?php endif; ?>
        <a href="ajouter_film.php">Ajouter un film</a>
        <a href="films.php">Tous les films</a>
    </div>

    <h2>Les 3 derniers films ajoutés</h2>

    <?php if (count($films) > 0): ?>
        <ul class="film-list">
            <?php foreach ($films as $film): ?>
                <li class="film-item">
                    <strong><?php echo htmlspecialchars($film['titre']); ?></strong>
                    <div class="film-info">
                        <strong>Réalisateur:</strong> <?php echo htmlspecialchars($film['realisateur']); ?><br>
                        <strong>Genre:</strong> <?php echo htmlspecialchars($film['genre']); ?><br>
                        <strong>Durée:</strong> <span class="duree"><?php echo formatDuree($film['duree']); ?></span>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="no-films">Aucun film dans la base de données.</p>
    <?php endif; ?>
</body>

</html>