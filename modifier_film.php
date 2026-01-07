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
$sql = "SELECT * FROM fiche_film WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(':id' => $film_id));
$film = $stmt->fetch();

if (!$film) {
    die("Film non trouvé.");
}

// Vérifier que l'utilisateur est le créateur du film
if ($film['user_id'] != $_SESSION['user_id']) {
    die("Vous n'êtes pas autorisé à modifier ce film.");
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
    if (!empty($titre) && !empty($realisateur) && 
        !empty($genre) && !empty($duree) && !empty($synopsis)) {
        // Garder l'ancienne image par défaut
        $image_name = $film['image'];

        // Si une nouvelle image est fournie
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = 'uploads/';
            
            // Créer le dossier s'il n'existe pas
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Récupérer l'extension du fichier
            $image_extension = pathinfo(
                $_FILES['image']['name'], 
                PATHINFO_EXTENSION
            );
            $image_name = uniqid() . '.' . $image_extension;
            $upload_path = $upload_dir . $image_name;

            // Déplacer le fichier
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Supprimer l'ancienne image si elle existe
                if ($film['image'] && 
                    file_exists($upload_dir . $film['image'])) {
                    unlink($upload_dir . $film['image']);
                }
            } else {
                $message = "Erreur lors de l'upload de l'image.";
                $image_name = $film['image'];
            }
        }

        // Si pas d'erreur, mettre à jour dans la base de données
        if (empty($message)) {
            $sql = "UPDATE fiche_film 
                    SET titre = :titre, realisateur = :realisateur, 
                        genre = :genre, duree = :duree, 
                        synopsis = :synopsis, image = :image 
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':titre' => $titre,
                ':realisateur' => $realisateur,
                ':genre' => $genre,
                ':duree' => $duree,
                ':synopsis' => $synopsis,
                ':image' => $image_name,
                ':id' => $film_id
            ));
            $message = "Film modifié avec succès !";

            // Recharger les données du film
            $sql = "SELECT * FROM fiche_film WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(':id' => $film_id));
            $film = $stmt->fetch();
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}

$page_title = 'Modifier un film';
$page_h1 = 'Modifier la fiche de film';
$nav_links = [
    ['url' => 'films.php', 'text' => 'Retour à la liste']
];
include 'includes/header.php';
?>
        <section>
            <?php if (!empty($message)): ?>
                <div class="success">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="titre">Titre :</label>
                    <input 
                        type="text" 
                        id="titre" 
                        name="titre" 
                        value="<?php echo htmlspecialchars($film['titre']); ?>" 
                        required>
                </div>

                <div class="form-group">
                    <label for="realisateur">Réalisateur :</label>
                    <input 
                        type="text" 
                        id="realisateur" 
                        name="realisateur"
                        value="<?php echo htmlspecialchars($film['realisateur']); ?>" 
                        required>
                </div>

                <div class="form-group">
                    <label for="genre">Genre :</label>
                    <input 
                        type="text" 
                        id="genre" 
                        name="genre" 
                        value="<?php echo htmlspecialchars($film['genre']); ?>" 
                        required>
                </div>

                <div class="form-group">
                    <label for="duree">Durée (en minutes) :</label>
                    <input type="number" id="duree" name="duree" value="<?php echo $film['duree']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="synopsis">Synopsis :</label>
                    <textarea 
                        id="synopsis" 
                        name="synopsis" 
                        rows="6"
                        required><?php echo htmlspecialchars($film['synopsis']); ?></textarea>
                </div>

                <?php if ($film['image']): ?>
                    <div class="form-group">
                        <label>Image actuelle :</label>
                        <img 
                            src="uploads/<?php echo htmlspecialchars($film['image']); ?>" 
                            alt="Image actuelle" 
                            width="200">
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="image">Changer l'image (optionnel) :</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>

                <button type="submit">Modifier le film</button>
            </form>
        </section>
<?php include 'includes/footer.php'; ?>