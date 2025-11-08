<div class="container">
    <h1>Управление бронированиями ресторана</h1>

    <?php 
    // === БЛОК ДЛЯ ОТОБРАЖЕНИЯ СООБЩЕНИЙ ===
    
    
    if (isset($_SESSION['success_message'])): ?>
        <p style="color: green; background-color: #e6ffe6; padding: 10px; border: 1px solid green; font-weight: bold;"><?php echo $_SESSION['success_message']; ?></p>
        <?php unset($_SESSION['success_message']); 
    endif;

    
    if (isset($_SESSION['error_message'])): ?>
        <p style="color: red; background-color: #ffe6e6; padding: 10px; border: 1px solid red; font-weight: bold;"><?php echo $_SESSION['error_message']; ?></p>
        <?php unset($_SESSION['error_message']); 
    endif;
    
    
    if (isset($error)): ?>
        <p style="color: red; font-weight: bold;"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <?php if (isset($restaurant) && isset($reservations)): ?>
        <h2>Бронирования для: <?php echo htmlspecialchars($restaurant['nom']); ?></h2>
        
        <?php if (empty($reservations)): ?>
            <p>На данный момент бронирования отсутствуют.</p>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="border: 1px solid #ddd; padding: 8px;">Клиент</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Дата</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Время</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Гости</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Пожелания</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Статус</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation): ?>
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            <?php echo htmlspecialchars($reservation['user_nom'] . ' ' . $reservation['user_prenom']); ?>
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px;"><?php echo htmlspecialchars($reservation['reservation_date']); ?></td>
                        <td style="border: 1px solid #ddd; padding: 8px;"><?php echo htmlspecialchars(substr($reservation['reservation_time'], 0, 5)); ?></td>
                        <td style="border: 1px solid #ddd; padding: 8px;"><?php echo htmlspecialchars($reservation['number_of_guests']); ?></td>
                        <td style="border: 1px solid #ddd; padding: 8px;"><?php echo htmlspecialchars($reservation['remarques'] ?? '-'); ?></td>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            <?php 
                                $status = $reservation['statut'];
                                if ($status === 'en attente') echo '<span style="color: orange;">В ожидании</span>';
                                else if ($status === 'confirmée') echo '<span style="color: green;">Подтверждено</span>';
                                else if ($status === 'annulée') echo '<span style="color: red;">Отменено</span>';
                                else echo htmlspecialchars($status);
                            ?>
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            <?php if ($reservation['statut'] === 'en attente'): ?>
                                <a href="?route=reservation/confirm&id=<?php echo $reservation['id']; ?>" style="color: green; margin-right: 10px;">Подтвердить</a>
                                <a href="?route=reservation/cancel&id=<?php echo $reservation['id']; ?>" style="color: red;">Отменить</a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    <?php elseif (isset($error)): ?>
        <p style="color: red; font-weight: bold;"><?php echo $error; ?></p>
        <?php if (!isset($restaurant)): ?>
            <p><a href="?route=restaurant/create">Создать ресторан</a></p>
        <?php endif; ?>
    <?php endif; ?>
</div>