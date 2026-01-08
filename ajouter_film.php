<?php
require_once 'config.php';
session_start();

// ! Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

// * Récupérer tous les genres et réalisateurs pour les listes déroulantes
$genres = $pdo->query("SELECT * FROM genre ORDER BY nom")->fetchAll();
$realisateurs = $pdo->query("SELECT * FROM realisateur ORDER BY nom")->fetchAll();

$message = '';

// * Traitement du formulaire d'ajout
if (isset($_POST['titre'])) {
    $titre = trim($_POST['titre']);
    $realisateur_id = trim($_POST['realisateur_id']);
    $nouveau_realisateur = trim($_POST['nouveau_realisateur'] ?? '');
    $genre_id = trim($_POST['genre_id']);
    $nouveau_genre = trim($_POST['nouveau_genre'] ?? '');
    $duree = trim($_POST['duree']);
    $synopsis = trim($_POST['synopsis']);

    // Ajouter un nouveau réalisateur si nécessaire
    if (!empty($nouveau_realisateur)) {
        $sql = "INSERT INTO realisateur (nom) VALUES (:nom)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':nom' => $nouveau_realisateur]);
        $realisateur_id = $pdo->lastInsertId();
    }

    // Ajouter un nouveau genre si nécessaire
    if (!empty($nouveau_genre)) {
        $sql = "INSERT INTO genre (nom) VALUES (:nom)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':nom' => $nouveau_genre]);
        $genre_id = $pdo->lastInsertId();
    }

    // * Validation des données
    if (empty($titre) || empty($duree) || empty($synopsis)) {
        $message = "Veuillez remplir tous les champs.";
    } elseif (strlen($titre) > 200) {
        $message = "Le titre est trop long (max 200 caractères).";
    } elseif (!is_numeric($duree) || $duree <= 0 || $duree > 1000) {
        $message = "La durée doit être un nombre entre 1 et 1000 minutes.";
    } elseif (empty($realisateur_id) || (!is_numeric($realisateur_id) && empty($nouveau_realisateur))) {
        $message = "Veuillez sélectionner un réalisateur.";
    } elseif (empty($genre_id) || (!is_numeric($genre_id) && empty($nouveau_genre))) {
        $message = "Veuillez sélectionner un genre.";
    } else {
        // ! Vérifier si le film existe déjà (pas de doublon)
        $sql = "SELECT id FROM fiche_film WHERE titre = :titre AND realisateur_id = :realisateur_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':titre' => $titre, ':realisateur_id' => $realisateur_id]);
        if ($stmt->fetch()) {
            $message = "Ce film existe déjà dans la base de données.";
        }

        $image_name = null;

        // * Gestion de l'upload d'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = 'uploads/';

            // ! Validation : taille max 5MB
            if ($_FILES['image']['size'] > 5242880) {
                $message = "L'image est trop volumineuse (max 5MB).";
            } else {
                // ! Validation : types MIME autorisés
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $_FILES['image']['tmp_name']);

                if (!in_array($mime_type, $allowed_types)) {
                    $message = "Type de fichier non autorisé. Utilisez JPG, PNG, GIF ou WEBP.";
                } else {
                    // Créer le dossier s'il n'existe pas
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }

                    // Extensions autorisées
                    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    $image_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                    if (!in_array($image_extension, $allowed_ext)) {
                        $message = "Extension de fichier non autorisée.";
                    } else {
                        $image_name = uniqid() . '.' . $image_extension;
                        $upload_path = $upload_dir . $image_name;

                        // Déplacer le fichier
                        if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                            $message = "Erreur lors de l'upload de l'image.";
                            $image_name = null;
                        }
                    }
                }
            }
        }

        // Si pas d'erreur, insérer dans la base de données
        if (empty($message)) {
            $sql = "INSERT INTO fiche_film 
                    (titre, realisateur_id, genre_id, duree, synopsis, image, user_id) 
                    VALUES 
                    (:titre, :realisateur_id, :genre_id, :duree, :synopsis, :image, :user_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':titre' => $titre,
                ':realisateur_id' => $realisateur_id,
                ':genre_id' => $genre_id,
                ':duree' => $duree,
                ':synopsis' => $synopsis,
                ':image' => $image_name,
                ':user_id' => $_SESSION['user_id']
            ));
            $message = "Film ajouté avec succès !";

            // Recharger les listes après ajout
            $genres = $pdo->query("SELECT * FROM genre ORDER BY nom")->fetchAll();
            $realisateurs = $pdo->query("SELECT * FROM realisateur ORDER BY nom")->fetchAll();
        }
    }
}

$page_title = 'Ajouter un film';
$page_h1 = 'Ajouter une fiche de film';
$nav_links = [
    ['url' => 'index.php', 'text' => 'Retour à l\'accueil']
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
            <input type="text" id="titre" name="titre" required>
        </div>

        <div class="form-group">
            <label for="realisateur_id">Réalisateur :</label>
            <select id="realisateur_id" name="realisateur_id" onchange="toggleNewRealisateur(this)">
                <option value="">-- Sélectionner --</option>
                <?php foreach ($realisateurs as $real): ?>
                    <option value="<?php echo $real['id']; ?>"><?php echo htmlspecialchars($real['nom']); ?></option>
                <?php endforeach; ?>
                <option value="nouveau">+ Ajouter un nouveau réalisateur</option>
            </select>
            <input type="text" id="nouveau_realisateur" name="nouveau_realisateur"
                placeholder="Nom du nouveau réalisateur" style="display:none; margin-top:10px;">
        </div>

        <div class="form-group">
            <label for="genre_id">Genre :</label>
            <select id="genre_id" name="genre_id" onchange="toggleNewGenre(this)">
                <option value="">-- Sélectionner --</option>
                <?php foreach ($genres as $g): ?>
                    <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['nom']); ?></option>
                <?php endforeach; ?>
                <option value="nouveau">+ Ajouter un nouveau genre</option>
            </select>
            <input type="text" id="nouveau_genre" name="nouveau_genre" placeholder="Nom du nouveau genre"
                style="display:none; margin-top:10px;">
        </div>

        <script>
            function toggleNewRealisateur(select) {
                const input = document.getElementById('nouveau_realisateur');
                if (select.value === 'nouveau') {
                    input.style.display = 'block';
                    input.required = true;
                    select.removeAttribute('required');
                } else {
                    input.style.display = 'none';
                    input.required = false;
                    select.required = true;
                }
            }
            function toggleNewGenre(select) {
                const input = document.getElementById('nouveau_genre');
                if (select.value === 'nouveau') {
                    input.style.display = 'block';
                    input.required = true;
                    select.removeAttribute('required');
                } else {
                    input.style.display = 'none';
                    input.required = false;
                    select.required = true;
                }
            }
        </script>

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
</section>
<?php include 'includes/footer.php'; ?>