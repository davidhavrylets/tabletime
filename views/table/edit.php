<div class="container">
    <h1>Modification de l'ID de la table: <?php echo htmlspecialchars($table['id'] ?? ''); ?></h1>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (isset($table) && isset($userRestaurant)): ?>
        <h2>Ресторан: <?php echo htmlspecialchars($userRestaurant['nom']); ?></h2>
        
        <form action="?route=table/edit&id=<?php echo $table['id']; ?>" method="POST" style="max-width: 400px;">
            
            <input type="hidden" name="table_id" value="<?php echo htmlspecialchars($table['id']); ?>">

            <div style="margin-bottom: 15px;">
                <label for="capacite">Capacité (nombre de personnes):</label>
                <input type="number" id="capacite" name="capacite" min="1" max="12" value="<?php echo htmlspecialchars($table['capacite']); ?>" required>
            </div>

            <button type="submit" style="padding: 10px 20px; background-color: #FFA500; color: white; border: none; cursor: pointer;">Sauvegarder les modifications</button>
            <a href="?route=table/manage" style="margin-left: 10px; color: gray;">Annuler</a>
        </form>

    <?php else: ?>
        <p>Impossible de charger les données de la table.</p>
    <?php endif; ?>
</div>