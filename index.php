<?php
require_once 'config.php';
require_once 'functions.php';
session_start();

// * Récupérer les 3 derniers films avec leurs relations
$sql = "SELECT f.*, g.nom as genre, r.nom as realisateur 
        FROM fiche_film f
        LEFT JOIN genre g ON f.genre_id = g.id
        LEFT JOIN realisateur r ON f.realisateur_id = r.id
        ORDER BY f.id DESC LIMIT 3";
$stmt = $pdo->query($sql);
$films = $stmt->fetchAll();

$page_title = 'Médiathèque';
$page_h1 = 'Médiathèque';
include 'includes/header.php';
?>
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
<?php include 'includes/footer.php'; ?>