<div class="container">
    <h1>Найдите свой идеальный столик</h1>
    
  <?php 
    
    if (isset($_SESSION['success_message'])): ?>
        <p style="color: green; background-color: #e6ffe6; padding: 10px; border: 1px solid green; font-weight: bold;">
            <?php echo $_SESSION['success_message']; ?>
        </p>
        <?php unset($_SESSION['success_message']); 
    endif;
    
    ?>

    <form action="?route=home" method="GET" class="search-form">
        <input type="hidden" name="route" value="home">
        
        <input type="text" name="search" placeholder="Название, адрес или описание" 
                value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" style="padding: 10px; width: 60%;">
        
        <select name="sort" style="padding: 10px;">
            <option value="id" <?php echo ($_GET['sort'] ?? '') === 'id' ? 'selected' : ''; ?>>По умолчанию</option>
            <option value="nom" <?php echo ($_GET['sort'] ?? '') === 'nom' ? 'selected' : ''; ?>>По имени</option>
            <option value="adresse" <?php echo ($_GET['sort'] ?? '') === 'adresse' ? 'selected' : ''; ?>>По адресу</option>
        </select>

        <select name="order" style="padding: 10px;">
            <option value="ASC" <?php echo ($_GET['order'] ?? '') === 'ASC' ? 'selected' : ''; ?>>По возрастанию</option>
            <option value="DESC" <?php echo ($_GET['order'] ?? '') === 'DESC' ? 'selected' : ''; ?>>По убыванию</option>
        </select>
        
        <button type="submit" style="padding: 10px;">Найти</button>
    </form>
    
    <hr>

    <?php if (empty($restaurants)): ?>
        <p>К сожалению, рестораны по вашему запросу не найдены.</p>
    <?php else: ?>
        <h3>Найдено ресторанов: <?php echo count($restaurants); ?></h3>
        
        <div class="restaurant-list">
            <?php foreach ($restaurants as $restaurant): ?>
                <div class="card" style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px;">
                    <h4><?php echo htmlspecialchars($restaurant['nom']); ?></h4>
                    <p><strong>Адрес:</strong> <?php echo htmlspecialchars($restaurant['adresse']); ?></p>
                    <p>
                        <?php 
                        $desc = $restaurant['description'] ?? 'Нет описания';
                        echo htmlspecialchars(substr($desc, 0, 150)) . (strlen($desc) > 150 ? '...' : ''); 
                        ?>
                    </p>
                    <a href="?route=reservation/create&restaurant_id=<?php echo htmlspecialchars($restaurant['id']); ?>" style="color: var(--color-primary); font-weight: bold;">Забронировать столик</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>