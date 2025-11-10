<div class="container">
    <h2>Список Ресторанов</h2>

    <?php 
    // Сообщения об успехе/ошибке (например, после бронирования)
    if (isset($_SESSION['success_message'])): ?>
        <p style="color: green; font-weight: bold;"><?php echo $_SESSION['success_message']; ?></p>
        <?php unset($_SESSION['success_message']); 
    endif; 
    if (isset($_SESSION['error_message'])): ?>
        <p style="color: red; font-weight: bold;"><?php echo $_SESSION['error_message']; ?></p>
        <?php unset($_SESSION['error_message']); 
    endif;
    ?>

    <?php if (empty($restaurants)): ?>
        <p>На данный момент нет доступных ресторанов.</p>
    <?php else: ?>
        <table class="table" border="1" cellpadding="10" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <th>Название (Nom)</th>
                    <th>Адрес (Adresse)</th>
                    <th>Описание (Description)</th>
                    <th>Бронирование</th> </tr>
            </thead>
            <tbody>
                <?php foreach ($restaurants as $restaurant): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($restaurant['nom']); ?></td>
                        <td><?php echo htmlspecialchars($restaurant['adresse']); ?></td>
                        <td>
                            <?php 
                            $desc = $restaurant['description'] ?? 'N/A';
                            echo htmlspecialchars(substr($desc, 0, 50)) . (strlen($desc) > 50 ? '...' : ''); 
                            ?>
                        </td>
                        
                        <td style="text-align: center;">
                            <a href="?route=reservation/create&id=<?php echo $restaurant['id']; ?>" style="padding: 5px 10px; background-color: #28a745; color: white; text-decoration: none;">
                                Забронировать
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>