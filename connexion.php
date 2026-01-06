<?php
require_once 'config.php';
session_start();

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $password = $_POST['password'];

    if (!empty($nom) && !empty($password)) {
        try {
            // Chercher l'utilisateur par son nom
            $stmt = $pdo->prepare("SELECT * FROM user WHERE nom = :nom");
            $stmt->execute([':nom' => $nom]);
            $user = $stmt->fetch();

            // Vérifier si l'utilisateur existe et si le mot de passe est correct
            if ($user && password_verify($password, $user['password'])) {
                // Connexion réussie : sauvegarder les infos dans la session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];

                // Rediriger vers la page d'accueil
                header('Location: index.php');
                exit;
            } else {
                $message = "Nom ou mot de passe incorrect.";
            }
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
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Connexion</h1>

    <form method="POST" action="">
        <div class="nav-links">
            <a href="index.php">Retour à l'accueil</a>
            <a href="inscription.php">S'inscrire</a>
        </div>

        <?php if (isset($message)): ?>
            <div class="error"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
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