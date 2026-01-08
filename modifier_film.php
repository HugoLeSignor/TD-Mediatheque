<?php
require_once 'config.php';
session_start();

// ! Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

// * Récupérer l'ID du film
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: films.php');
    exit;
}

$film_id = intval($_GET['id']);

// * Récupérer le film avec son genre et réalisateur
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

// Vérifier que l'utilisateur est le créateur du film
if ($film['user_id'] != $_SESSION['user_id']) {
    die("Vous n'êtes pas autorisé à modifier ce film.");
}

// Récupérer tous les genres et réalisateurs pour les listes déroulantes
$genres = $pdo->query("SELECT * FROM genre ORDER BY nom")->fetchAll();
$realisateurs = $pdo->query("SELECT * FROM realisateur ORDER BY nom")->fetchAll();

$message = '';

// Si le formulaire est envoyé
if (isset($_POST['titre'])) {
    $titre = trim($_POST['titre']);
    $realisateur_id = trim($_POST['realisateur_id']);
    $nouveau_realisateur = trim($_POST['nouveau_realisateur'] ?? '');
    $genre_id = trim($_POST['genre_id']);
    $nouveau_genre = trim($_POST['nouveau_genre'] ?? '');
    $duree = trim($_POST['duree']);
    $synopsis = trim($_POST['synopsis']);

    // Gérer l'ajout d'un nouveau réalisateur
    if (!empty($nouveau_realisateur)) {
        $sql = "INSERT INTO realisateur (nom) VALUES (:nom)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':nom' => $nouveau_realisateur]);
        $realisateur_id = $pdo->lastInsertId();
    }

    // Gérer l'ajout d'un nouveau genre
    if (!empty($nouveau_genre)) {
        $sql = "INSERT INTO genre (nom) VALUES (:nom)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':nom' => $nouveau_genre]);
        $genre_id = $pdo->lastInsertId();
    }

    // Validation des données
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
    } elseif (empty($message)) {
        // Garder l'ancienne image par défaut
        $image_name = $film['image'];

        // Si une nouvelle image est fournie
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = 'uploads/';

            // Validation : taille max 5MB
            if ($_FILES['image']['size'] > 5242880) {
                $message = "L'image est trop volumineuse (max 5MB).";
            } else {
                // Validation : types MIME autorisés
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $_FILES['image']['tmp_name']);

                if (!in_array($mime_type, $allowed_types)) {
                    $message = "Type de fichier non autorisé.";
                    $image_name = $film['image'];
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
                        $image_name = $film['image'];
                    } else {
                        $image_name = uniqid() . '.' . $image_extension;
                        $upload_path = $upload_dir . $image_name;

                        // Déplacer le fichier
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
                }
            }
        }

        // Si pas d'erreur, mettre à jour dans la base de données
        if (empty($message)) {
            $sql = "UPDATE fiche_film 
                    SET titre = :titre, realisateur_id = :realisateur_id, 
                        genre_id = :genre_id, duree = :duree, 
                        synopsis = :synopsis, image = :image 
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':titre' => $titre,
                ':realisateur_id' => $realisateur_id,
                ':genre_id' => $genre_id,
                ':duree' => $duree,
                ':synopsis' => $synopsis,
                ':image' => $image_name,
                ':id' => $film_id
            ));
            $message = "Film modifié avec succès !";

            // Recharger les données du film avec jointures
            $sql = "SELECT f.*, g.nom as genre, r.nom as realisateur 
                    FROM fiche_film f
                    LEFT JOIN genre g ON f.genre_id = g.id
                    LEFT JOIN realisateur r ON f.realisateur_id = r.id
                    WHERE f.id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(':id' => $film_id));
            $film = $stmt->fetch();

            // Recharger les listes après modification
            $genres = $pdo->query("SELECT * FROM genre ORDER BY nom")->fetchAll();
            $realisateurs = $pdo->query("SELECT * FROM realisateur ORDER BY nom")->fetchAll();
        }
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
            <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($film['titre']); ?>" required>
        </div>

        <div class="form-group">
            <label for="realisateur_id">Réalisateur :</label>
            <select id="realisateur_id" name="realisateur_id" onchange="toggleNewRealisateur(this)">
                <option value="">-- Sélectionner --</option>
                <?php foreach ($realisateurs as $real): ?>
                    <option value="<?php echo $real['id']; ?>" <?php echo ($film['realisateur_id'] == $real['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($real['nom']); ?>
                    </option>
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
                    <option value="<?php echo $g['id']; ?>" <?php echo ($film['genre_id'] == $g['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($g['nom']); ?>
                    </option>
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
            <input type="number" id="duree" name="duree" value="<?php echo $film['duree']; ?>" required>
        </div>

        <div class="form-group">
            <label for="synopsis">Synopsis :</label>
            <textarea id="synopsis" name="synopsis" rows="6"
                required><?php echo htmlspecialchars($film['synopsis']); ?></textarea>
        </div>

        <?php if ($film['image']): ?>
            <div class="form-group">
                <label>Image actuelle :</label>
                <img src="uploads/<?php echo htmlspecialchars($film['image']); ?>" alt="Image actuelle" width="200">
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