<?php
require_once 'config.php';
session_start();

$message = '';

// Si le formulaire est envoyé
if (isset($_POST['nom']) && isset($_POST['password'])) {
    $nom = trim($_POST['nom']);
    $password = $_POST['password'];

    // Vérifier que les champs ne sont pas vides
    if (empty($nom) || empty($password)) {
        $message = "Veuillez remplir tous les champs.";
    } else {
        // Chercher l'utilisateur dans la base de données
        $sql = "SELECT * FROM user WHERE nom = :nom";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':nom' => $nom));
        $user = $stmt->fetch();

        // Vérifier le mot de passe
        if ($user && password_verify($password, $user['password'])) {
            // Connecter l'utilisateur
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_prenom'] = $user['prenom'];
            
            // Rediriger vers la page d'accueil
            header('Location: index.php');
            exit;
        } else {
            $message = "Nom ou mot de passe incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Connexion</h1>

    <div class="nav-links">
        <a href="index.php">Retour à l'accueil</a>
        <a href="inscription.php">S'inscrire</a>
    </div>

    <?php if (!empty($message)): ?>
        <div class="error"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit">Se connecter</button>
    </form>
</body>
</html>