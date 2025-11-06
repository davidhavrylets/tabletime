<div class="container">
    <h2>Ajouter un nouveau Restaurant</h2>

    <?php if (isset($success)): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="?route=restaurant/create" method="POST">
        <div>
            <label for="nom">Nom du restaurant:</label>
            <input type="text" name="nom" required>
        </div>
        <div>
            <label for="adresse">Adresse:</label>
            <input type="text" name="adresse" required>
        </div>
        <div>
            <label for="description">Description:</label>
            <textarea name="description" rows="5"></textarea>
        </div>
        
        <input type="hidden" name="user_id_restaurateur" value="<?php echo $_SESSION['user_id'] ?? ''; ?>">
        
        <button type="submit">Créer le Restaurant</button>
    </form>
    <p><a href="?route=restaurant/list">Retour à la liste</a></p>
</div>