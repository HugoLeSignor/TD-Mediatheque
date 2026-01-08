<?php
require_once 'config.php';
require_once 'functions.php';
session_start();

// Récupérer tous les films avec leurs genres et réalisateurs
$sql = "SELECT f.*, g.nom as genre, r.nom as realisateur 
        FROM fiche_film f
        LEFT JOIN genre g ON f.genre_id = g.id
        LEFT JOIN realisateur r ON f.realisateur_id = r.id
        ORDER BY f.id DESC";
$stmt = $pdo->query($sql);
$films = $stmt->fetchAll();

$page_title = 'Tous les films';
$page_h1 = 'Tous les films';
$nav_links = [
    ['url' => 'index.php', 'text' => 'Retour à l\'accueil'],
    ['url' => 'ajouter_film.php', 'text' => 'Ajouter un film']
];
include 'includes/header.php';
?>
<section>
    <h2>
        Liste complète (<?php echo count($films); ?> films)
    </h2>

    <?php if (count($films) > 0): ?>
        <?php foreach ($films as $film): ?>
            <article class="film-card">
                <?php if ($film['image']): ?>
                    <img src="uploads/<?php echo htmlspecialchars($film['image']); ?>"
                        alt="<?php echo htmlspecialchars($film['titre']); ?>" width="200">
                <?php endif; ?>

                <div>
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

                    <nav class="actions">
                        <a href="detail_film.php?id=<?php echo $film['id']; ?>">Voir plus</a>

                        <?php if (
                            isset($_SESSION['user_id']) &&
                            $_SESSION['user_id'] == $film['user_id']
                        ): ?>
                            <a href="modifier_film.php?id=<?php echo $film['id']; ?>">
                                Modifier
                            </a>
                            <a href="supprimer_film.php?id=<?php echo $film['id']; ?>" class="delete"
                                onclick="return confirm('Voulez-vous vraiment supprimer ce film ?')">
                                Supprimer
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-films">Aucun film dans la base de données.</p>
    <?php endif; ?>
</section>
<?php include 'includes/footer.php'; ?>