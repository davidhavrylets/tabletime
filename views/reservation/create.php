<div class="container">
    <h2>Бронирование столика в: <?php echo htmlspecialchars($restaurant['nom'] ?? 'Ресторан'); ?></h2>
    
    <?php 
    // Блок для ошибок из сессии (после перенаправления)
    if (isset($_SESSION['error_message'])): ?>
        <p style="color: red; font-weight: bold;"><?php echo $_SESSION['error_message']; ?></p>
        <?php unset($_SESSION['error_message']); 
    endif;
    
    // Блок для локальных ошибок контроллера
    if (isset($error)): ?>
        <p style="color: red; font-weight: bold;"><?php echo $error; ?></p>
    <?php endif; 
    
    
    if (!$restaurant): ?>
        <p>Невозможно продолжить бронирование без выбранного ресторана.</p>
        <a href="?route=home">Вернуться к списку ресторанов</a>
    <?php else: ?>
    
        <form action="?route=reservation/create&id=<?php echo htmlspecialchars($restaurant['id']); ?>" method="POST">
            
            <input type="hidden" name="restaurant_id" value="<?php echo htmlspecialchars($restaurant['id']); ?>">

            <div style="margin-bottom: 15px;">
                <label for="number_of_guests">Количество гостей (мест):</label>
                <input type="number" id="number_of_guests" name="number_of_guests" required min="1" value="2">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="reservation_date">Дата:</label>
                <input type="date" id="reservation_date" name="reservation_date" required min="<?php echo date('Y-m-d'); ?>">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="reservation_time">Время:</label>
                <input type="time" id="reservation_time" name="reservation_time" required step="1800">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="remarques">Пожелания (необязательно):</label>
                <textarea id="remarques" name="remarques" rows="3"></textarea>
            </div>
            
            <button type="submit">Забронировать Столик</button>
        </form>
        
    <?php endif; ?>
</div>