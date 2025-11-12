<?php

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TableTime - Réservation de Tables</title>
    <link rel="stylesheet" href="assets/css/style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40JefC1/hw9gq1W2h1b/Q3n/JgD0w2o22yXW/t6U0t0B/A6b5sF6D5xQ2uQ2/u/Q2nQ1Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />       
</head>
<body>
    <header>
    <div class="header-inner">
        
        <a href="?route=home" class="logo">
            <img 
                src="assets/images/tabletime_logo.png" 
                alt="TableTime Logo" 
                style="height: 40px; vertical-align: middle;" 
                loading="lazy"
            >
        </a>
        
        <nav class="mobile-bottom-nav">
    <ul>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['user_role'] === 'client'): ?>
                <li><a href="?route=reservation/list"><i class="fas fa-calendar-alt"></i> Réservations</a></li>
                <li><a href="?route=user/profile"><i class="fas fa-user"></i> Profil</a></li>
                <li><a href="?route=logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            <?php elseif ($_SESSION['user_role'] === 'owner'): ?>
                <li><a href="?route=restaurant/list"><i class="fas fa-utensils"></i> Restaurants</a></li>
                <li><a href="?route=reservation/manage"><i class="fas fa-tasks"></i> Reservations</a></li>
                <li><a href="?route=logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            <?php endif; ?>
        <?php else: ?>
            <li><a href="?route=register"><i class="fas fa-user-plus"></i> S'inscrire</a></li>
            <li><a href="?route=login"><i class="fas fa-sign-in-alt"></i> Se connecter</a></li>
        <?php endif; ?>
    </ul>
</nav>
        
    </div>
</header>
    <main>