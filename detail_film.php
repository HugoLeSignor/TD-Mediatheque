<?php
require_once 'config.php';
require_once 'functions.php';
session_start();

// Récupérer l'ID du film
if (!isset($_GET['id'])) {
    header('Location: films.php');
    exit;
}

$film_id = $_GET['id'];

// Récupérer le film
$sql = "SELECT * FROM fiche_film WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(':id' => $film_id));
$film = $stmt->fetch();

if (!$film) {
    die("Film non trouvé.");
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($film['titre']); ?></title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1><?php echo htmlspecialchars($film['titre']); ?></h1>

    <div class="nav-links">
        <a href="films.php">Retour à la liste</a>
        <a href="index.php">Retour à l'accueil</a>
    </div>

    <div class="film-detail">
        <?php if ($film['image']): ?>
            <img src="uploads/<?php echo htmlspecialchars($film['image']); ?>"
                alt="<?php echo htmlspecialchars($film['titre']); ?>" width="300">
        <?php endif; ?>

        <div class="film-info">
            <p><strong>Réalisateur :</strong> <?php echo htmlspecialchars($film['realisateur']); ?></p>
            <p><strong>Genre :</strong> <?php echo htmlspecialchars($film['genre']); ?></p>
            <p><strong>Durée :</strong> <span class="duree"><?php echo formatDuree($film['duree']); ?></span></p>
        </div>

        <h2>Synopsis</h2>
        <div class="synopsis">
            <?php echo nl2br(htmlspecialchars($film['synopsis'])); ?>
        </div>
    </div>
</body>

</html>