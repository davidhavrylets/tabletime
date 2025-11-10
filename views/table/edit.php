<div class="container">
    <h1>Редактирование Столика ID: <?php echo htmlspecialchars($table['id'] ?? ''); ?></h1>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (isset($table) && isset($userRestaurant)): ?>
        <h2>Ресторан: <?php echo htmlspecialchars($userRestaurant['nom']); ?></h2>
        
        <form action="?route=table/edit&id=<?php echo $table['id']; ?>" method="POST" style="max-width: 400px;">
            
            <input type="hidden" name="table_id" value="<?php echo htmlspecialchars($table['id']); ?>">

            <div style="margin-bottom: 15px;">
                <label for="name">Название Столика (например, "Окно", "Зал 1"):</label>
                <input type="text" id="name" name="name" 
                       value="<?php echo htmlspecialchars($table['name'] ?? ''); ?>" required> 
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="capacite">Вместимость (Количество человек):</label>
                <input type="number" id="capacite" name="capacite" min="1" max="12" value="<?php echo htmlspecialchars($table['capacite']); ?>" required>
            </div>

            <button type="submit" style="padding: 10px 20px; background-color: #FFA500; color: white; border: none; cursor: pointer;">Сохранить Изменения</button>
            <a href="?route=table/manage" style="margin-left: 10px; color: gray;">Отмена</a>
        </form>

    <?php else: ?>
        <p>Не удалось загрузить данные столика.</p>
    <?php endif; ?>
</div>