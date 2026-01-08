<?php
require_once 'config.php';
session_start();

$message = '';

// * Traitement du formulaire de connexion
if (isset($_POST['nom']) && isset($_POST['password'])) {
    $nom = trim($_POST['nom']);
    $password = $_POST['password'];

    // ! Validation des champs
    if (empty($nom) || empty($password)) {
        $message = "Veuillez remplir tous les champs.";
    } else {
        // * Rechercher l'utilisateur dans la base
        $sql = "SELECT * FROM user WHERE nom = :nom";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':nom' => $nom));
        $user = $stmt->fetch();

        // Vérifier le mot de passe
        if ($user && password_verify($password, $user['password'])) {
            // Régénérer l'ID de session pour éviter le vol de session
            session_regenerate_id(true);

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

$page_title = 'Connexion';
$page_h1 = 'Connexion';
$nav_links = [
    ['url' => 'index.php', 'text' => 'Retour à l\'accueil'],
    ['url' => 'inscription.php', 'text' => 'S\'inscrire']
];
include 'includes/header.php';
?>
<section>
    <?php if (!empty($message)): ?>
        <div class="error">
            <?php echo htmlspecialchars($message); ?>
        </div>
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
</section>
<?php include 'includes/footer.php'; ?>