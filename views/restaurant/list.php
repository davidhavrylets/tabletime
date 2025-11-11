<div class="container">
    <h2>Список Ресторанов</h2>

    <?php 
    // Вывод "прилипших" сообщений из сессии
    if (isset($_SESSION['success_message'])): ?>
        <p style="color: green; font-weight: bold;"><?php echo $_SESSION['success_message']; ?></p>
        <?php unset($_SESSION['success_message']); 
    endif; 

    if (isset($_SESSION['error_message'])): ?>
        <p style="color: red; font-weight: bold;"><?php echo $_SESSION['error_message']; ?></p>
        <?php unset($_SESSION['error_message']); 
    endif;
    ?>
    
    <p>
        <a href="?route=restaurant/create" class="btn btn-primary">
            ➕ Добавить новый ресторан
        </a>
    </p>

    <?php if (empty($restaurants)): ?>
        <p>У вас пока нет ресторанов.</p>
    <?php else: ?>
        <table class="table-styled">
            <thead>
                <tr>
                    <th>Название (Nom)</th>
                    <th>Адрес (Adresse)</th>
                    <th>Описание (Description)</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($restaurants as $restaurant): ?>
                <tr>
                    <td><?php echo htmlspecialchars($restaurant['nom']); ?></td>
                    <td><?php echo htmlspecialchars($restaurant['adresse']); ?></td>
                    <td><?php echo htmlspecialchars($restaurant['description']); ?></td>
                    
                    <td>
                        <a href="?route=table/manage&restaurant_id=<?php echo $restaurant['id']; ?>" class="btn btn-primary btn-sm">
                            🍽️ Столики
                        </a>
                        
                        <a href="?route=restaurant/edit&id=<?php echo $restaurant['id']; ?>" class="btn btn-secondary btn-sm">
                            ✏️ Редактировать
                        </a>
                        <a href="?route=restaurant/delete&id=<?php echo $restaurant['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Уверены, что хотите удалить <?php echo htmlspecialchars($restaurant['nom']); ?>?');">
                            🗑️ Удалить
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>