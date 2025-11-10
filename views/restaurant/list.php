<div class="container">
    <h2>Список Ресторанов</h2>

    <?php 
    
    if (isset($_SESSION['success_message'])): ?>
        <p style="color: green; font-weight: bold;"><?php echo $_SESSION['success_message']; ?></p>
        <?php unset($_SESSION['success_message']); 
    endif; 

    if (isset($_SESSION['error_message'])): ?>
        <p style="color: red; font-weight: bold;"><?php echo $_SESSION['error_message']; ?></p>
        <?php unset($_SESSION['error_message']); 
    endif;
    ?>
    <p><a href="?route=restaurant/create" class="btn btn-primary">Добавить новый ресторан</a></p>

    <?php 
    
    if (empty($restaurants)): 
    ?>
        <p>Пока не зарегистрирован ни один ресторан.</p>
    <?php else: ?>
        <table class="table" border="1" cellpadding="10" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название (Nom)</th>
                    <th>Адрес (Adresse)</th>
                    <th>Описание (Description)</th>
                    <th>ID Владельца</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($restaurants as $restaurant): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($restaurant['id']); ?></td>
                        <td><?php echo htmlspecialchars($restaurant['nom']); ?></td>
                        <td><?php echo htmlspecialchars($restaurant['adresse']); ?></td>
                        <td>
                            <?php 
                    
                            $desc = $restaurant['description'] ?? 'N/A';
                            echo htmlspecialchars(substr($desc, 0, 50)) . (strlen($desc) > 50 ? '...' : ''); 
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($restaurant['UTILISATEUR_id'] ?? 'N/A'); ?></td>
                        <td>
                            <a href="#">Посмотреть</a>
                            <a href="?route=restaurant/edit&id=<?php echo htmlspecialchars($restaurant['id']); ?>" 
                               style="margin-right: 10px;">Редактировать</a>
                            <a href="?route=restaurant/delete&id=<?php echo htmlspecialchars($restaurant['id']); ?>" 
                               onclick="return confirm('Вы уверены, что хотите удалить ресторан «<?php echo htmlspecialchars($restaurant['nom']); ?>»?');" 
                               style="color: red; margin-left: 10px;">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>