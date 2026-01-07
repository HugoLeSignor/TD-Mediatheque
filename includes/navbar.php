<nav class="nav-links">
    <?php if (isset($nav_links)): ?>
        <?php foreach ($nav_links as $link): ?>
            <a href="<?php echo htmlspecialchars($link['url']); ?>">
                <?php echo htmlspecialchars($link['text']); ?>
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <?php // Navigation par défaut ?>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="deconnexion.php">Se déconnecter</a>
        <?php else: ?>
            <a href="connexion.php">Se connecter</a>
            <a href="inscription.php">S'inscrire</a>
        <?php endif; ?>
        <a href="ajouter_film.php">Ajouter un film</a>
        <a href="films.php">Tous les films</a>
        <a href="index.php">Accueil</a>
    <?php endif; ?>
</nav>

