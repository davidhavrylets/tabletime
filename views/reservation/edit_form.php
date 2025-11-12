<?php

if (!isset($reservation) || !isset($restaurant)) {

    echo '<div class="container"><div class="alert alert-error">Erreur: Données de réservation non trouvées.</div></div>';
    return;
}


$currentGuests = htmlspecialchars($reservation['number_of_guests']);
$currentDate = htmlspecialchars($reservation['reservation_date']);

$currentTime = htmlspecialchars(substr($reservation['reservation_time'], 0, 5));
$currentRemarques = htmlspecialchars($reservation['remarques'] ?? '');

?>

<div class="container">
    
    <div class="form-container">
        
        <h1>Modifier la réservation</h1>

        <p>Vous modifiez la réservation au restaurant : 
            <strong><?php echo htmlspecialchars($restaurant['nom']); ?></strong>
        </p>

        <?php 
        // Отображение сообщений об ошибках, если они были установлены в контроллере
        if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['error_message']; ?>
            </div>
            <?php unset($_SESSION['error_message']); 
        endif; ?>

        <form method="POST" action="?route=reservation/edit&id=<?php echo $reservation['id']; ?>" class="styled-form">

            <div class="form-group">
                <label for="reservation_date">Date :</label>
                <input 
                    type="date" 
                    id="reservation_date" 
                    name="reservation_date" 
                    value="<?php echo $currentDate; ?>" 
                    required
                >
            </div>

            <div class="form-group">
                <label for="reservation_time">Heure :</label>
                <input 
                    type="time" 
                    id="reservation_time" 
                    name="reservation_time" 
                    value="<?php echo $currentTime; ?>" 
                    step="1800"
                    required
                >
            </div>
            
            <div class="form-group">
                <label for="number_of_guests">Nombre de convives :</label>
                <input 
                    type="number" 
                    id="number_of_guests" 
                    name="number_of_guests" 
                    min="1" 
                    value="<?php echo $currentGuests; ?>" 
                    required
                >
            </div>

            <div class="form-group">
                <label for="remarques">Souhaits / Commentaires (facultatif) :</label>
                <textarea 
                    id="remarques" 
                    name="remarques" 
                    rows="3"
                ><?php echo $currentRemarques; ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Mettre à jour la réservation</button>
            
            <a href="?route=reservation/list" class="btn btn-link mt-20" style="display: block; text-align: center;">Annuler et revenir à la liste</a>

        </form>
    </div>
</div>