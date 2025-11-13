<div class="container">
    <h1>Mon compte</h1>
    
    <?php 
    // Ваш код для вывода общих сообщений (если они переданы из контроллера)
    if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php 
    // Используем сессии для сообщений, если контроллер их устанавливает
    if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; ?></div>
        <?php unset($_SESSION['success_message']); 
    endif;
    if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-error"><?php echo $_SESSION['error_message']; ?></div>
        <?php unset($_SESSION['error_message']); 
    endif;
    ?>

    <?php if (isset($user_data)): ?>
    
        <div class="profile-layout">
            
            <div class="profile-card data-card">
                <h2>Données personnelles</h2>
                
                

                <form method="POST" action="?route=user/profile" class="styled-form profile-form">
                    
                    <div class="form-group">
                        <label for="prenom">Prénom:</label>
                        <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user_data['prenom'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="nom">Nom:</label>
                        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user_data['nom'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="telephone">Téléphone:</label>
                        <input type="text" id="telephone" name="telephone" value="<?php echo htmlspecialchars($user_data['telephone'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input 
                            type="email" 
                            id="email" 
                            value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" 
                            readonly 
                            class="readonly-field"
                        >
                        <small>Email est votre identifiant unique et ne peut pas être modifié.</small>
                    </div>

                    <input type="hidden" name="action_type" value="update_info"> 
                    
                    <button type="submit" class="btn btn-primary">
                        Mettre à jour les informations
                    </button>
                </form>
            </div>

            <div class="profile-card password-card">
                <h2> Changer le mot de passe</h2>
                <p>Utilisez ce formulaire pour changer votre mot de passe.</p>

                <form method="POST" action="?route=user/profile" class="styled-form password-form">
                    
                    <div class="form-group">
                        <label for="current_password">Mot de passe actuel:</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">Nouveau mot de passe:</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirmer le nouveau mot de passe:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <input type="hidden" name="action_type" value="change_password">
                    
                    <button type="submit" class="btn btn-warning">
                        Changer le mot de passe
                    </button>
                </form>
            </div>
            
        </div> <?php else: ?>
        <p>Impossible de charger les données de l'utilisateur.</p>
    <?php endif; ?>
</div>