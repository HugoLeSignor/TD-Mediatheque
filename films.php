<?php
require_once 'config.php';
require_once 'functions.php';
session_start();

// Récupérer tous les films
$sql = "SELECT * FROM fiche_film ORDER BY id DESC";
$stmt = $pdo->query($sql);
$films = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tous les films</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Tous les films</h1>
        <nav class="nav-links">
            <a href="index.php">Retour à l'accueil</a>
            <a href="ajouter_film.php">Ajouter un film</a>
        </nav>
    </header>

    <main>
        <section>
            <h2>
                Liste complète (<?php echo count($films); ?> films)
            </h2>

            <?php if (count($films) > 0): ?>
                <?php foreach ($films as $film): ?>
                    <article class="film-card">
                        <h3><?php echo htmlspecialchars($film['titre']); ?></h3>

                        <?php if ($film['image']): ?>
                            <img 
                                src="uploads/<?php echo htmlspecialchars($film['image']); ?>"
                                alt="<?php echo htmlspecialchars($film['titre']); ?>" 
                                width="200">
                        <?php endif; ?>

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

                        <nav class="actions">
                            <a href="detail_film.php?id=<?php echo $film['id']; ?>">Voir plus</a>

                            <?php if (isset($_SESSION['user_id']) && 
                                      $_SESSION['user_id'] == $film['user_id']): ?>
                                <a href="modifier_film.php?id=<?php echo $film['id']; ?>">
                                    Modifier
                                </a>
                                <a href="supprimer_film.php?id=<?php echo $film['id']; ?>" 
                                   class="delete"
                                   onclick="return confirm('Voulez-vous vraiment supprimer ce film ?')">
                                    Supprimer
                                </a>
                            <?php endif; ?>
                        </nav>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-films">Aucun film dans la base de données.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>