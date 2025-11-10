<div class="container">
    <h1>Регистрация</h1>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="?route=register" method="POST" style="max-width: 400px; margin: 0 auto;">
        
        <div style="margin-bottom: 15px;">
            <label for="prenom">Имя (Prenom):</label>
            <input type="text" id="prenom" name="prenom" required>
        </div>
        
        <div style="margin-bottom: 15px;">
            <label for="nom">Фамилия (Nom):</label>
            <input type="text" id="nom" name="nom" required>
        </div>
        
        <div style="margin-bottom: 15px;">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div style="margin-bottom: 15px;">
            <label for="telephone">Телефон (необязательно):</label>
            <input type="text" id="telephone" name="telephone">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <hr style="margin: 20px 0;">

        <div id="owner-code-field" style="margin-bottom: 15px; display: none;">
            <label for="secret_code">Секретный Код Владельца:</label>
            <input type="text" id="secret_code" name="secret_code" placeholder="Введите код для администратора">
        </div>
        
        <button type="submit" name="register_client" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; cursor: pointer;">
            Зарегистрироваться как Клиент
        </button>
        
        <button type="submit" id="owner-register-btn" name="register_owner" style="padding: 10px 20px; background-color: #ffc107; color: black; border: none; cursor: pointer; margin-left: 10px; display: none;">
            Подтвердить Регистрацию Владельца
        </button>
        
        <button type="button" id="toggle_owner_register" style="padding: 10px 20px; background-color: #ffc107; color: black; border: none; cursor: pointer; margin-left: 10px;">
            Я - Владелец Ресторана
        </button>
    </form>
</div>

<script src="assets/js/auth.js" defer></script>