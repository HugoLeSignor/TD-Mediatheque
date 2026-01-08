<?php
require_once 'config.php';
require_once 'functions.php';
session_start();

// * Récupérer l'ID du film
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: films.php');
    exit;
}

$film_id = intval($_GET['id']);

// * Récupérer le film avec ses relations (genre et réalisateur)
$sql = "SELECT f.*, g.nom as genre, r.nom as realisateur 
        FROM fiche_film f
        LEFT JOIN genre g ON f.genre_id = g.id
        LEFT JOIN realisateur r ON f.realisateur_id = r.id
        WHERE f.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(':id' => $film_id));
$film = $stmt->fetch();

if (!$film) {
    die("Film non trouvé.");
}

$page_title = htmlspecialchars($film['titre']);
$page_h1 = htmlspecialchars($film['titre']);
$nav_links = [
    ['url' => 'films.php', 'text' => 'Retour à la liste'],
    ['url' => 'index.php', 'text' => 'Retour à l\'accueil']
];
include 'includes/header.php';
?>
<article class="film-detail">
    <?php if ($film['image']): ?>
        <img src="uploads/<?php echo htmlspecialchars($film['image']); ?>"
            alt="<?php echo htmlspecialchars($film['titre']); ?>" width="300">
    <?php endif; ?>

    <section class="film-info">
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
    </section>

    <section>
        <h2>Synopsis</h2>
        <div class="synopsis">
            <?php echo nl2br(htmlspecialchars($film['synopsis'])); ?>
        </div>
    </section>
</article>
<?php include 'includes/footer.php'; ?>