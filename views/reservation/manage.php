<div class="container">
    <h1>Управление бронированиями ресторана</h1>

    <?php 
    
    
    // Сообщение об успехе
    if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; ?></div>
        <?php unset($_SESSION['success_message']); 
    endif;

    
    if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-error"><?php echo $_SESSION['error_message']; ?></div>
        <?php unset($_SESSION['error_message']); 
    endif;
    
    // Локальная ошибка
    if (isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (isset($restaurant) && isset($reservations)): ?>
        <h2>Бронирования для: <?php echo htmlspecialchars($restaurant['nom']); ?></h2>
        
        <?php if (empty($reservations)): ?>
            <p>На данный момент бронирования отсутствуют.</p>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Клиент</th>
                            <th>Дата</th>
                            <th>Время</th>
                            <th class="text-center">Гости</th>
                            <th>Пожелания</th>
                            <th class="text-center">Статус</th>
                            <th class="text-center">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $reservation): 
                            // Определяем класс для подсветки статуса
                            $status = $reservation['statut'];
                            $statusText = '';
                            $statusClass = '';
                            
                            if ($status === 'en attente') {
                                $statusText = 'В ожидании';
                                $statusClass = 'text-warning';
                            } else if ($status === 'confirmée') {
                                $statusText = 'Подтверждено';
                                $statusClass = 'text-success';
                            } else if ($status === 'annulée') {
                                $statusText = 'Отменено';
                                $statusClass = 'text-danger';
                            } else {
                                $statusText = htmlspecialchars($status);
                            }
                        ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($reservation['user_nom'] . ' ' . $reservation['user_prenom']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($reservation['reservation_date']); ?></td>
                            <td><?php echo htmlspecialchars(substr($reservation['reservation_time'], 0, 5)); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($reservation['number_of_guests']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['remarques'] ?? '-'); ?></td>
                            
                            <td class="text-center">
                                <span class="<?php echo $statusClass; ?>">
                                    **<?php echo $statusText; ?>**
                                </span>
                            </td>
                            
                            <td class="text-center">
                                <?php if ($status === 'en attente'): ?>
                                    <a href="?route=reservation/confirm&id=<?php echo $reservation['id']; ?>" class="btn-link text-success">
                                        Подтвердить
                                    </a>
                                    <a href="?route=reservation/cancel&id=<?php echo $reservation['id']; ?>" 
                                       class="btn-link text-danger" 
                                       onclick="return confirm('Вы уверены, что хотите отменить бронирование?');">
                                        Отменить
                                    </a>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    <?php elseif (isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
        <?php if (!isset($restaurant)): ?>
            <p><a href="?route=restaurant/create" class="btn-link">Создать ресторан</a></p>
        <?php endif; ?>
    <?php endif; ?>
</div>