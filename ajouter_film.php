<?php
require_once 'config.php';
session_start();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

$message = '';

// Si le formulaire est envoyé
if (isset($_POST['titre'])) {
    $titre = trim($_POST['titre']);
    $realisateur = trim($_POST['realisateur']);
    $genre = trim($_POST['genre']);
    $duree = trim($_POST['duree']);
    $synopsis = trim($_POST['synopsis']);

    // Vérifier que tous les champs sont remplis
    if (empty($titre) || empty($realisateur) || empty($genre) || empty($duree) || empty($synopsis)) {
        $message = "Veuillez remplir tous les champs.";
    } else {
        $image_name = null;

        // Gérer l'upload de l'image si elle existe
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = 'uploads/';

            // Créer le dossier s'il n'existe pas
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Récupérer l'extension du fichier
            $image_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = uniqid() . '.' . $image_extension;
            $upload_path = $upload_dir . $image_name;

            // Déplacer le fichier
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $message = "Erreur lors de l'upload de l'image.";
                $image_name = null;
            }
        }

        // Si pas d'erreur, insérer dans la base de données
        if (empty($message)) {
            $sql = "INSERT INTO fiche_film (titre, realisateur, genre, duree, synopsis, image, user_id) VALUES (:titre, :realisateur, :genre, :duree, :synopsis, :image, :user_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':titre' => $titre,
                ':realisateur' => $realisateur,
                ':genre' => $genre,
                ':duree' => $duree,
                ':synopsis' => $synopsis,
                ':image' => $image_name,
                ':user_id' => $_SESSION['user_id']
            ));
            $message = "Film ajouté avec succès !";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Ajouter un film</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Ajouter une fiche de film</h1>

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="nav-links">
            <a href="index.php">Retour à l'accueil</a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="form-group">
            <label for="titre">Titre :</label>
            <input type="text" id="titre" name="titre" required>
        </div>

        <div class="form-group">
            <label for="realisateur">Réalisateur :</label>
            <input type="text" id="realisateur" name="realisateur" required>
        </div>

        <div class="form-group">
            <label for="genre">Genre :</label>
            <input type="text" id="genre" name="genre" required>
        </div>

        <div class="form-group">
            <label for="duree">Durée (en minutes) :</label>
            <input type="number" id="duree" name="duree" required>
        </div>

        <div class="form-group">
            <label for="synopsis">Synopsis :</label>
            <textarea id="synopsis" name="synopsis" rows="6" required></textarea>
        </div>

        <div class="form-group">
            <label for="image">Image (optionnel) :</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>

        <button type="submit">Ajouter le film</button>
    </form>
</body>

</html>