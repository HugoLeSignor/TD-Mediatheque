<?php
require_once 'config.php';
session_start();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

// Récupérer l'ID du film
if (!isset($_GET['id'])) {
    header('Location: films.php');
    exit;
}

$film_id = $_GET['id'];

// Récupérer le film
try {
    $stmt = $pdo->prepare("SELECT * FROM fiche_film WHERE id = :id");
    $stmt->execute([':id' => $film_id]);
    $film = $stmt->fetch();

    if (!$film) {
        die("Film non trouvé.");
    }

    // Vérifier que l'utilisateur est le créateur du film
    if ($film['user_id'] != $_SESSION['user_id']) {
        die("Vous n'êtes pas autorisé à modifier ce film.");
    }
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $realisateur = trim($_POST['realisateur']);
    $genre = trim($_POST['genre']);
    $duree = trim($_POST['duree']);
    $synopsis = trim($_POST['synopsis']);

    if (!empty($titre) && !empty($realisateur) && !empty($genre) && !empty($duree) && !empty($synopsis)) {
        // Gérer l'upload de l'image si une nouvelle image est fournie
        $image_name = $film['image']; // Garder l'ancienne image par défaut

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $image_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = uniqid() . '.' . $image_extension;
            $upload_path = $upload_dir . $image_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Supprimer l'ancienne image si elle existe
                if ($film['image'] && file_exists($upload_dir . $film['image'])) {
                    unlink($upload_dir . $film['image']);
                }
            } else {
                $message = "Erreur lors de l'upload de l'image.";
                $image_name = $film['image'];
            }
        }

        try {
            $stmt = $pdo->prepare("UPDATE fiche_film SET titre = :titre, realisateur = :realisateur, genre = :genre, duree = :duree, synopsis = :synopsis, image = :image WHERE id = :id");
            $stmt->execute([
                ':titre' => $titre,
                ':realisateur' => $realisateur,
                ':genre' => $genre,
                ':duree' => $duree,
                ':synopsis' => $synopsis,
                ':image' => $image_name,
                ':id' => $film_id
            ]);
            $message = "Film modifié avec succès !";

            // Recharger les données du film
            $stmt = $pdo->prepare("SELECT * FROM fiche_film WHERE id = :id");
            $stmt->execute([':id' => $film_id]);
            $film = $stmt->fetch();
        } catch (PDOException $e) {
            $message = "Erreur : " . $e->getMessage();
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier un film</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Modifier la fiche de film</h1>

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="nav-links">
            <a href="films.php">Retour à la liste</a>
        </div>

        <?php if (isset($message)): ?>
            <div class="success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <div class="form-group">
            <label for="titre">Titre :</label>
            <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($film['titre']); ?>" required>
        </div>

        <div class="form-group">
            <label for="realisateur">Réalisateur :</label>
            <input type="text" id="realisateur" name="realisateur"
                value="<?php echo htmlspecialchars($film['realisateur']); ?>" required>
        </div>

        <div class="form-group">
            <label for="genre">Genre :</label>
            <input type="text" id="genre" name="genre" value="<?php echo htmlspecialchars($film['genre']); ?>" required>
        </div>

        <div class="form-group">
            <label for="duree">Durée (en minutes) :</label>
            <input type="number" id="duree" name="duree" value="<?php echo $film['duree']; ?>" required>
        </div>

        <div class="form-group">
            <label for="synopsis">Synopsis :</label>
            <textarea id="synopsis" name="synopsis" rows="6"
                required><?php echo htmlspecialchars($film['synopsis']); ?></textarea>
        </div>

        <?php if ($film['image']): ?>
            <div>
                <strong>Image actuelle :</strong>
                <br><img src="uploads/<?php echo htmlspecialchars($film['image']); ?>" alt="Image actuelle" width="200">
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="image">Changer l'image (optionnel) :</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>

        <button type="submit">Modifier le film</button>
    </form>
</body>

</html>