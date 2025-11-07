<div class="container">
    <h2>Управление Столиками для: <?php echo htmlspecialchars($userRestaurant['nom'] ?? 'Вашего Ресторана'); ?></h2>

    <?php 
    // Вывод сообщений
    if (isset($_SESSION['success_message'])): ?>
        <p style="color: green; font-weight: bold;"><?php echo $_SESSION['success_message']; ?></p>
        <?php unset($_SESSION['success_message']); 
    endif; 

    if (isset($_SESSION['error_message'])): ?>
        <p style="color: red; font-weight: bold;"><?php echo $_SESSION['error_message']; ?></p>
        <?php unset($_SESSION['error_message']); 
    endif;
    
    if (isset($error)): ?>
        <p style="color: red; font-weight: bold;"><?php echo $error; ?></p>
    <?php endif; 
    ?>
    
    <hr>
    
    <h3>➕ Добавить Новый Столик</h3>
    <form action="?route=table/manage" method="POST" style="margin-bottom: 30px;">
        <label for="capacite">Вместимость столика (Кол-во мест):</label>
        <input type="number" name="capacite" required min="1" style="width: 150px; margin-right: 15px;">
        <button type="submit">Добавить Столик</button>
    </form>
    
    <h3>📋 Ваши Столики</h3>
    <?php if (empty($tables)): ?>
        <p>У вас еще нет зарегистрированных столиков.</p>
    <?php else: ?>
        <table class="table" border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>ID Столика</th>
                    <th>Вместимость</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tables as $table): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($table['id']); ?></td>
                        <td><?php echo htmlspecialchars($table['capacite']); ?> мест</td>
                        <td>
                            <a href="#">Редактировать</a> | <a href="#" style="color: red;">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>