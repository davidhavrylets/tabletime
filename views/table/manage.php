<div class="container">
    <h2>Gestion des Tables pour: <?php echo htmlspecialchars($userRestaurant['nom'] ?? 'Votre Restaurant'); ?></h2>

    <?php 
    if (isset($_SESSION['success_message'])): ?>
        <p style="color: green; font-weight: bold;"><?php echo $_SESSION['success_message']; ?></p>
        <?php unset($_SESSION['success_message']); 
    endif; 

    if (isset($_SESSION['error_message'])): ?>
        <p style="color: red; font-weight: bold;"><?php echo $_SESSION['error_message']; ?></p>
        <?php unset($_SESSION['error_message']); 
    endif;
    
    if (isset($error)): ?>
        <p style="color: red; font-weight: bold;"><?php echo $error; ?></p>
    <?php endif; 
    ?>
    
    <hr>

    <h3> Ajouter Nouveau Table</h3>

    <form action="?route=table/manage&restaurant_id=<?php echo htmlspecialchars($userRestaurant['id']); ?>" method="POST" style="margin-bottom: 30px;">
        
        <div class="form-group" style="margin-bottom: 15px;">
             <label for="numero">Nom/Numéro de la table:</label>
             <input type="text" id="numero" name="numero" required placeholder="Ex. 'Table 1' ou 'Fenêtre'">
        </div>

        <div class="form-group">
            <label for="capacite">Capacité de la table (Nombre de places):</label>
            <input type="number" id="capacite" name="capacite" required min="1">
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Ajouter une table</button>
    </form>
    
    <h3> Vos Tables</h3>
    <?php if (empty($tables)): ?>
        <p>Vous n'avez pas encore de tables enregistrées.</p>
    <?php else: ?>
        <table class="table" border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Nom de la table</th> 
                    <th>Capacité</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tables as $table): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($table['numero']); ?></td> 
                        <td><?php echo htmlspecialchars($table['capacite']); ?> мест</td>
                        <td>
                            <a href="?route=table/edit&id=<?php echo $table['id']; ?>&restaurant_id=<?php echo $userRestaurant['id']; ?>" style="color: blue;">
                                Modifier
                            </a> | 
                            
                            <a href="?route=table/delete&id=<?php echo $table['id']; ?>&restaurant_id=<?php echo $userRestaurant['id']; ?>" 
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer la table \'<?php echo htmlspecialchars($table['numero']); ?>\'?');" 
                               style="color: red;">
                                Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <br>
    <a href="?route=restaurant/list" class="btn btn-secondary">
        &larr; Retour à la liste des restaurants
    </a>
</div>