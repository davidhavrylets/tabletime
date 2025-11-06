<div class="container">
    <h2>Редактировать Ресторан: <?php echo htmlspecialchars($restaurant['nom'] ?? ''); ?></h2>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="?route=restaurant/edit&id=<?php echo htmlspecialchars($restaurant['id'] ?? ''); ?>" method="POST">
        <div>
            <label for="nom">Nom du restaurant:</label>
            <input type="text" name="nom" value="<?php echo htmlspecialchars($restaurant['nom'] ?? ''); ?>" required>
        </div>
        <div>
            <label for="adresse">Adresse:</label>
            <input type="text" name="adresse" value="<?php echo htmlspecialchars($restaurant['adresse'] ?? ''); ?>" required>
        </div>
        <div>
            <label for="description">Description:</label>
            <textarea name="description" rows="5"><?php echo htmlspecialchars($restaurant['description'] ?? ''); ?></textarea>
        </div>
        
        <button type="submit">Sauvegarder les modifications</button>
    </form>
    <p><a href="?route=restaurant/list">Retour à la liste</a></p>
</div>