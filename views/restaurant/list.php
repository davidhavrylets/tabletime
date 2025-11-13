<div class="container">
    <h2>Liste des Restaurants</h2>

    <?php 
    
    if (isset($_SESSION['success_message'])): ?>
        <p style="color: green; font-weight: bold;"><?php echo $_SESSION['success_message']; ?></p>
        <?php unset($_SESSION['success_message']); 
    endif; 

    if (isset($_SESSION['error_message'])): ?>
        <p style="color: red; font-weight: bold;"><?php echo $_SESSION['error_message']; ?></p>
        <?php unset($_SESSION['error_message']); 
    endif;
    ?>
    
    <p>
        <a href="?route=restaurant/create" class="btn btn-primary">
             Ajouter un nouveau restaurant
        </a>
    </p>

    <?php if (empty($restaurants)): ?>
        <p>Vous n'avez pas encore de restaurants.</p>
    <?php else: ?>
        <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Adresse</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($restaurants as $restaurant): ?>
                <tr>
                    <td><?php echo htmlspecialchars($restaurant['nom']); ?></td>
                    <td><?php echo htmlspecialchars($restaurant['adresse']); ?></td>
                    <td><?php echo htmlspecialchars($restaurant['description']); ?></td>
                    
                    <td>
                        <a href="?route=table/manage&restaurant_id=<?php echo $restaurant['id']; ?>" class="btn btn-primary btn-sm">
                             Tables
                        </a>
                        
                        <a href="?route=restaurant/edit&id=<?php echo $restaurant['id']; ?>" class="btn btn-secondary btn-sm">
                             Modifier
                        </a>
                        <a href="?route=restaurant/delete&id=<?php echo $restaurant['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer <?php echo htmlspecialchars($restaurant['nom']); ?> ?');">
                             Supprimer
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="table-container">
    <?php endif; ?>
</div>