<div class="container">
    <h1>Mes réservations</h1>

    <?php 
    
    if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success_message']; ?>
        </div>
        <?php unset($_SESSION['success_message']); 
    endif;
    
    ?>

    <?php if (empty($reservations)): ?>
        <p>Vous n'avez actuellement aucune réservation active ou terminée.</p>
        <p><a href="?route=home" class="btn-link">Trouver et réserver une table</a></p>
    <?php else: ?>
        
        <div class="reservation-list">
            
            <?php foreach ($reservations as $reservation): 
                
                // Логика статусов остается прежней
                $status = $reservation['statut'];
                $displayStatus = '';
                $statusClass = ''; // Класс для подсветки/цвета
                $cardStatusClass = ''; // Класс для привязки стилей CSS к карточке
                
                if ($status === 'en attente') {
                    $displayStatus = 'En attendant';
                    $statusClass = 'text-warning'; 
                    $cardStatusClass = 'status-en-attente'; // Используем французский для CSS-класса
                } else if ($status === 'confirmée') {
                    $displayStatus = 'Confirmée';
                    $statusClass = 'text-success'; 
                    $cardStatusClass = 'status-confirmée';
                } else if ($status === 'annulée') {
                    $displayStatus = 'Annulée';
                    $statusClass = 'text-danger'; 
                    $cardStatusClass = 'status-annulée';
                } else {
                    $displayStatus = htmlspecialchars($status);
                }
            ?>
            
            <div class="reservation-card <?php echo $cardStatusClass; ?>">
                
                <div class="card-details">
                    <p class="restaurant-name">
                        <?php echo htmlspecialchars($reservation['restaurant_nom']); ?>
                    </p>
                    <div class="card-info">
                        <p class="date"> Date: <strong><?php echo htmlspecialchars($reservation['reservation_date']); ?></strong></p>
                        <p class="time"> Heure: <strong><?php echo htmlspecialchars(substr($reservation['reservation_time'], 0, 5)); ?></strong></p>
                        <p class="people"> Invités : <strong><?php echo htmlspecialchars($reservation['number_of_guests']); ?></strong></p>
                    </div>
                </div>
                
                <div class="card-status">
                    <span class="status-badge <?php echo $statusClass; ?>">
                        <?php echo $displayStatus; ?>
                    </span>
                </div>
                
                <div class="card-actions">
                    <?php if ($status === 'en attente' || $status === 'confirmée'): ?>
                        <a href="?route=reservation/edit&id=<?php echo $reservation['id']; ?>" class="btn btn-warning">Modifier</a>
                        
                        <form method="POST" action="?route=reservation/cancel">
                            <input type="hidden" name="id" value="<?php echo $reservation['id']; ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir annuler votre réservation ?');">Annuler</button>
                        </form>
                    <?php endif; ?>
                    
                </div>
            </div>
            
            <?php endforeach; ?>
            
        </div>
        <?php endif; ?>
</div>