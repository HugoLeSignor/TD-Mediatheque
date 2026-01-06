<?php
require_once 'config.php';

$message = '';

// Si le formulaire est envoyé
if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['password'])) {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $password = $_POST['password'];

    // Vérifier que tous les champs sont remplis
    if (!empty($nom) && !empty($prenom) && !empty($password)) {
        // Crypter le mot de passe
        $password_crypte = password_hash($password, PASSWORD_DEFAULT);

        // Insérer dans la base de données
        try {
            $sql = "INSERT INTO user (nom, prenom, password) VALUES (:nom, :prenom, :password)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':password' => $password_crypte
            ));
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

        <?php if (!empty($message)): ?>
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