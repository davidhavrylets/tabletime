<div class="container">
    <h1>Мои бронирования</h1>

    <?php 
    // === БЛОК ДЛЯ ОТОБРАЖЕНИЯ FLASH MESSAGES (Сюда тоже нужно добавить!) ===
    if (isset($_SESSION['success_message'])): ?>
        <p style="color: green; background-color: #e6ffe6; padding: 10px; border: 1px solid green; font-weight: bold;">
            <?php echo $_SESSION['success_message']; ?>
        </p>
        <?php unset($_SESSION['success_message']); 
    endif;
    // ===========================================
    ?>

    <?php if (empty($reservations)): ?>
        <p>У вас пока нет активных или завершенных бронирований.</p>
        <p><a href="?route=home">Найти и забронировать столик</a></p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="border: 1px solid #ddd; padding: 8px;">Ресторан</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Дата</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Время</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Гости</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Статус</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $reservation): ?>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;"><?php echo htmlspecialchars($reservation['restaurant_nom']); ?></td>
                    <td style="border: 1px solid #ddd; padding: 8px;"><?php echo htmlspecialchars($reservation['reservation_date']); ?></td>
                    <td style="border: 1px solid #ddd; padding: 8px;"><?php echo htmlspecialchars(substr($reservation['reservation_time'], 0, 5)); ?></td>
                    <td style="border: 1px solid #ddd; padding: 8px;"><?php echo htmlspecialchars($reservation['number_of_guests']); ?></td>
                    <td style="border: 1px solid #ddd; padding: 8px;">
                        <?php 
                            // Перевод статуса для пользователя
                            $status = $reservation['statut'];
                            if ($status === 'en attente') echo 'В ожидании';
                            else if ($status === 'confirmée') echo 'Подтверждено';
                            else if ($status === 'annulée') echo 'Отменено';
                            else echo htmlspecialchars($status);
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>