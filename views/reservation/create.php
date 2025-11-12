<div class="container">
    <div class="form-container">
        <h2>Réservation de table à: <?php echo htmlspecialchars($restaurant['nom'] ?? 'Restaurant'); ?></h2>
        
        <?php 
        // Сообщения об успехе/ошибках: 
        if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error"><?php echo $_SESSION['error_message']; ?></div>
            <?php unset($_SESSION['error_message']); 
        endif;
        
        if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; 
        
        
        if (!$restaurant): ?>
            <p>Il est impossible de poursuivre la réservation sans avoir sélectionné un restaurant.</p>
            <a href="?route=home" class="btn-link">Retour à la liste des restaurants</a>
        <?php else: ?>
        
            <form action="?route=reservation/create&id=<?php echo htmlspecialchars($restaurant['id']); ?>" method="POST">
                
                <input type="hidden" name="restaurant_id" value="<?php echo htmlspecialchars($restaurant['id']); ?>">

                <div class="form-group">
                    <label for="number_of_guests">Nombre de convives (places) :</label>
                    <input type="number" id="number_of_guests" name="number_of_guests" required min="1" value="2">
                </div>

                <div class="form-group">
                    <label for="reservation_date">Date:</label>
                    <input type="date" id="reservation_date" name="reservation_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label for="reservation_time">Heure:</label>
                    <input type="time" id="reservation_time" name="reservation_time" required step="1800"> 
                </div>

                <div class="form-group">
                    <label for="remarques">Souhaits (facultatif) :</label>
                    <textarea id="remarques" name="remarques" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn btn-success">Réserver une table</button>
            </form>
            
        <?php endif; ?>
    </div>
</div>