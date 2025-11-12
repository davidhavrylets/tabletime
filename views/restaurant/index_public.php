<div class="container">
    <h2>Liste des restaurants</h2>

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

    <?php if (empty($restaurants)): ?>
        <p>Il n'y a actuellement aucun restaurant disponible.</p>
    <?php else: ?>
        
        <section class="restaurant-list">
            
            <?php foreach ($restaurants as $restaurant): ?>
            
            <div class="restaurant-card">
                
                <div class="card-image-container">
    <img 
        src="assets/images/restaurants/<?php 
            
            echo htmlspecialchars($restaurant['photo_filename'] ?? 'placeholder.png'); 
        ?>" 
        alt="Фотография ресторана <?php echo htmlspecialchars($restaurant['nom']); ?>"
    >
</div>

                <div class="card-content">
                    <h2 class="card-title"><?php echo htmlspecialchars($restaurant['nom']); ?></h2>
                    
                    <p class="card-address">
                        <i class="fas fa-map-marker-alt"></i> 
                        <?php echo htmlspecialchars($restaurant['adresse']); ?>
                    </p>
                    
                    <p class="card-description">
                        <?php 
                        
                        $desc = htmlspecialchars($restaurant['description'] ?? '');
                        echo strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc; 
                        ?>
                    </p>
                    
                    <div class="card-footer">
                        <a 
                            href="?route=reservation/create&restaurant_id=<?php echo $restaurant['id']; ?>" 
                            class="btn btn-primary">
                            Réserver
                        </a>
                    </div>
                </div>
            </div>
            
            <?php endforeach; ?>
            
        </section>
        <?php endif; ?>
</div>