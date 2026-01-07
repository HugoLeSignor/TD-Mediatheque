<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Médiathèque'; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <?php if (isset($page_h1)): ?>
            <h1><?php echo htmlspecialchars($page_h1); ?></h1>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <aside class="user-info">
                <strong>
                    Bonjour 
                    <?php echo htmlspecialchars($_SESSION['user_nom']); ?>
                    <?php echo htmlspecialchars($_SESSION['user_prenom']); ?>
                </strong>
            </aside>
        <?php endif; ?>

        <?php include __DIR__ . '/navbar.php'; ?>
    </header>

    <main>

