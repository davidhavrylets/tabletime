<div class="container">
    <h1>Мои бронирования</h1>

    <?php 
    // === БЛОК ДЛЯ ОТОБРАЖЕНИЯ FLASH MESSAGES (Используем .alert) ===
    if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success_message']; ?>
        </div>
        <?php unset($_SESSION['success_message']); 
    endif;
    // ===========================================
    ?>

    <?php if (empty($reservations)): ?>
        <p>У вас пока нет активных или завершенных бронирований.</p>
        <p><a href="?route=home" class="btn-link">Найти и забронировать столик</a></p>
    <?php else: ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ресторан</th>
                        <th>Дата</th>
                        <th>Время</th>
                        <th>Гости</th>
                        <th>Статус</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reservation['restaurant_nom']); ?></td>
                        <td><?php echo htmlspecialchars($reservation['reservation_date']); ?></td>
                        <td><?php echo htmlspecialchars(substr($reservation['reservation_time'], 0, 5)); ?></td>
                        <td><?php echo htmlspecialchars($reservation['number_of_guests']); ?></td>
                        
                        <td>
                            <?php 
                                $status = $reservation['statut'];
                                $displayStatus = '';
                                $statusClass = ''; // Класс для подсветки

                                if ($status === 'en attente') {
                                    $displayStatus = 'В ожидании';
                                    $statusClass = 'text-warning'; // Используем .text-warning
                                } else if ($status === 'confirmée') {
                                    $displayStatus = 'Подтверждено';
                                    $statusClass = 'text-success'; // Класс .text-success
                                } else if ($status === 'annulée') {
                                    $displayStatus = 'Отменено';
                                    $statusClass = 'text-danger'; // Используем .text-danger
                                } else {
                                    $displayStatus = htmlspecialchars($status);
                                }
                            ?>
                            <span class="<?php echo $statusClass; ?>">
                                **<?php echo $displayStatus; ?>**
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>