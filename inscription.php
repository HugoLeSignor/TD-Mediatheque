<?php
require_once 'config.php';

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $password = $_POST['password'];

    if (!empty($nom) && !empty($prenom) && !empty($password)) {
        // Crypter le mot de passe
        $password_crypte = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO user (nom, prenom, password) VALUES (:nom, :prenom, :password)");
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':password' => $password_crypte
            ]);
            $message = "Inscription réussie !";
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
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Inscription</h1>

    <form method="POST" action="">
        <div class="nav-links">
            <a href="index.php">Retour à l'accueil</a>
            <a href="connexion.php">Se connecter</a>
        </div>

        <?php if (isset($message)): ?>
            <div class="success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>
        </div>

        <div class="form-group">
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" required>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit">S'inscrire</button>
    </form>
</body>

</html>