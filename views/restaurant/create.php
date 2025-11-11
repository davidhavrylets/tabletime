<div class="container">
    
    <div class="form-container">
        <h2>Ajouter un nouveau Restaurant</h2>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

                <form action="?route=restaurant/create" method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label for="nom">Nom du restaurant:</label>
                <input type="text" id="nom" name="nom" required> 
            </div>
            
            <div class="form-group">
                <label for="adresse">Adresse:</label>
                <input type="text" id="adresse" name="adresse" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="5"></textarea>
            </div>

            <div class="form-group">
                <label for="photo">Photo du Restaurant (JPG, PNG, WebP):</label>
                <input type="file" id="photo" name="photo" accept="image/jpeg, image/png, image/webp" class="form-control">
            </div>
                        
            <input type="hidden" name="user_id_restaurateur" value="<?php echo $_SESSION['user_id'] ?? ''; ?>">
            
            <button type="submit" class="btn btn-primary">Créer le Restaurant</button>
        </form>
    </div>
    
    <p><a href="?route=restaurant/list" class="btn-link">Retour à la liste</a></p>
</div>