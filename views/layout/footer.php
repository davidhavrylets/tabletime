
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
</main>

<footer class="site-footer">
    <div class="footer-container">
        
        <div class="footer-col footer-nav">
            <h4 class="footer-title">Navigation</h4>
            <ul>
                <li><a href="?route=home">Accueil</a></li>
                <li><a href="?route=contact">Contact</a></li>
                <li><a href="?route=login">Connexion</a></li>
            </ul>
        </div>

        <div class="footer-col footer-legal">
            <h4 class="footer-title">Informations Légales</h4>
            <ul>
                <li><a href="?route=association">Association</a></li>
                <li><a href="?route=mentions_legales">Mentions légales</a></li>
                <li><a href="?route=politique_confidentialite">Politique de confidentialité</a></li>
                <li><a href="?route=cgu">Conditions Générales d'Utilisation</a></li>
                <li><a href="?route=cgv">CGV</a></li>
            </ul>
        </div>

        <div class="footer-col footer-contact">
            <h4 class="footer-title">Contact & Réseaux</h4>
            
            <div class="contact-item">
                <i class="fas fa-envelope"></i> 
                <a href="mailto:tabletime@gmail.com" class="footer-email">tabletime@gmail.com</a>
            </div>
            
            <div class="social-links">
                <a href="#" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
        </div>

    </div>
    
    <div class="footer-bottom">
        <p>© 2025 David Havrylets - TableTime | Tous droits réservés</p>
    </div>
</footer>

<div id="cookie-consent-banner">
    <div class="content">
        <p>
            Nous utilisons des cookies pour améliorer votre expérience sur notre site. 
            En continuant à utiliser notre service, vous acceptez notre 
            <a href="?route=cgu">politique de confidentialité et nos conditions générales d'utilisation</a>
        </p>
        <div class="buttons">
            <button id="cookie-accept" class="btn btn-success">Accepter</button>
            <button id="cookie-reject" class="btn btn-secondary">Refuser</button>
        </div>
    </div>
</div>

<script src="assets/js/auth.js" ></script> 
<script src="assets/js/cookie_consent.js" ></script> 

</body>
</html>