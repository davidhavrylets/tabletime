<div class="container">
    <h2>Управление Столиками для: <?php echo htmlspecialchars($userRestaurant['nom'] ?? 'Вашего Ресторана'); ?></h2>

    <?php 
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
    
    <form action="?route=table/manage&restaurant_id=<?php echo htmlspecialchars($userRestaurant['id']); ?>" method="POST" style="margin-bottom: 30px;">
        
        <div class="form-group" style="margin-bottom: 15px;">
             <label for="numero">Имя/Номер Столика:</label>
             <input type="text" id="numero" name="numero" required placeholder="Напр. 'Столик 1' или 'Окно'">
        </div>

        <div class="form-group">
            <label for="capacite">Вместимость столика (Кол-во мест):</label>
            <input type="number" id="capacite" name="capacite" required min="1">
        </div>
        
        <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Добавить Столик</button>
    </form>
    
    <h3>📋 Ваши Столики</h3>
    <?php if (empty($tables)): ?>
        <p>У вас еще нет зарегистрированных столиков.</p>
    <?php else: ?>
        <table class="table" border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Имя Столика</th> 
                    <th>Вместимость</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tables as $table): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($table['numero']); ?></td> 
                        <td><?php echo htmlspecialchars($table['capacite']); ?> мест</td>
                        <td>
                            <a href="?route=table/edit&id=<?php echo $table['id']; ?>&restaurant_id=<?php echo $userRestaurant['id']; ?>" style="color: blue;">
                                Редактировать
                            </a> | 
                            
                            <a href="?route=table/delete&id=<?php echo $table['id']; ?>&restaurant_id=<?php echo $userRestaurant['id']; ?>" 
                               onclick="return confirm('Вы уверены, что хотите удалить столик \'<?php echo htmlspecialchars($table['numero']); ?>\'?');" 
                               style="color: red;">
                                Удалить
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <br>
    <a href="?route=restaurant/list" class="btn btn-secondary">
        &larr; Вернуться к списку ресторанов
    </a>
</div>