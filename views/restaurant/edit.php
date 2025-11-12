<div class="container">
    <div class="form-container">
        <h2>Modifier Restaurant : <?php echo htmlspecialchars($restaurant['nom'] ?? ''); ?></h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="?route=restaurant/edit&id=<?php echo htmlspecialchars($restaurant['id'] ?? ''); ?>" method="POST">
            
            <div class="form-group">
                <label for="nom">Nom du restaurant:</label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($restaurant['nom'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="adresse">Adresse:</label>
                <input type="text" id="adresse" name="adresse" value="<?php echo htmlspecialchars($restaurant['adresse'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($restaurant['description'] ?? ''); ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-success">
                Sauvegarder les modifications
            </button>
        </form>
    </div>
    
    <p class="mt-20 text-center">
        <a href="?route=restaurant/list" class="btn-link">
            &#8592; Retour à la liste
        </a>
    </p>
</div>