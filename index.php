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
        <div class="film-grid">
            <?php foreach ($films as $film): ?>
                <a href="detail_film.php?id=<?php echo $film['id']; ?>" class="film-poster">
                    <?php if ($film['image']): ?>
                        <img src="uploads/<?php echo htmlspecialchars($film['image']); ?>"
                            alt="<?php echo htmlspecialchars($film['titre']); ?>">
                    <?php else: ?>
                        <div class="placeholder">
                            <span>🎬</span>
                            <p><?php echo htmlspecialchars($film['titre']); ?></p>
                        </div>
                    <?php endif; ?>
                    <div class="film-title-overlay">
                        <?php echo htmlspecialchars($film['titre']); ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="no-films">Aucun film dans la base de données.</p>
    <?php endif; ?>
</section>
<?php include 'includes/footer.php'; ?>