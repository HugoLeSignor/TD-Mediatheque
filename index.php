<?php
require_once 'config.php';
require_once 'functions.php';
session_start();

// Récupérer les 3 derniers films
$sql = "SELECT * FROM fiche_film ORDER BY id DESC LIMIT 3";
$stmt = $pdo->query($sql);
$films = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Médiathèque</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Médiathèque</h1>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <aside class="user-info">
                <strong>
                    Bonjour 
                    <?php echo htmlspecialchars($_SESSION['user_nom']); ?>
                    <?php echo htmlspecialchars($_SESSION['user_prenom']); ?>
                </strong>
            </aside>
        <?php endif; ?>

        <nav class="nav-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="deconnexion.php">Se déconnecter</a>
            <?php else: ?>
                <a href="connexion.php">Se connecter</a>
                <a href="inscription.php">S'inscrire</a>
            <?php endif; ?>
            <a href="ajouter_film.php">Ajouter un film</a>
            <a href="films.php">Tous les films</a>
        </nav>
    </header>

    <main>
        <section>
            <h2>Les 3 derniers films ajoutés</h2>

            <?php if (count($films) > 0): ?>
                <ul class="film-list">
                    <?php foreach ($films as $film): ?>
                        <li class="film-item">
                            <article>
                                <h3><?php echo htmlspecialchars($film['titre']); ?></h3>
                                <div class="film-info">
                                    <p>
                                        <strong>Réalisateur :</strong> 
                                        <?php echo htmlspecialchars($film['realisateur']); ?>
                                    </p>
                                    <p>
                                        <strong>Genre :</strong> 
                                        <?php echo htmlspecialchars($film['genre']); ?>
                                    </p>
                                    <p>
                                        <strong>Durée :</strong> 
                                        <span class="duree">
                                            <?php echo formatDuree($film['duree']); ?>
                                        </span>
                                    </p>
                                </div>
                            </article>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="no-films">Aucun film dans la base de données.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>