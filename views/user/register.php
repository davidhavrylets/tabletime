<div class="container register-form-container">
    <h2>Inscription</h2>

    <?php if (isset($error)): ?>
        <p class="error-message" style="color: red; font-weight: bold;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="?route=register" method="POST" class="styled-form">
        
        <div class="form-group">
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($nom ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="mot_de_passe">Mot de passe :</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
        </div>

        <div class="form-group">
            <label for="mot_de_passe_confirm">Confirmer le mot de passe:</label>
            <input type="password" id="mot_de_passe_confirm" name="mot_de_passe_confirm" required>
        </div>
        
        <hr>
        
        <div class="form-group">
            <label for="owner_code">Code Propriétaire (Facultatif)</label>
            <input 
                type="text" 
                id="owner_code" 
                name="owner_code" 
                class="form-control" 
                placeholder="Entrez le code si vous êtes propriétaire"
                value="<?php echo htmlspecialchars($_POST['owner_code'] ?? ''); ?>"
            >
            <small class="form-text text-muted">Laissez vide si vous êtes un client normal.</small>
        </div>

        <div class="form-group privacy-checkbox">
            <input type="checkbox" id="privacy_policy" name="privacy_policy" required>
            <label for="privacy_policy">Je suis d'accord avec <a href="#" target="_blank">la Politique de confidentialité</a> et les conditions d'utilisation.</label>
        </div>
        
        <button type="submit" class="btn btn-primary">S'inscrire</button>
    </form>
    
    <p class="link-to-login">
        Déjà un compte? <a href="?route=login">Connectez-vous</a>
    </p>

</div>