<div class="container register-form-container">
    <h2>Регистрация</h2>

    <?php if (isset($error)): ?>
        <p class="error-message" style="color: red; font-weight: bold;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="?route=register" method="POST" class="styled-form">
        
        <div class="form-group">
            <label for="nom">Ваше Имя (Nom):</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($nom ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email (Используется для входа):</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="mot_de_passe">Пароль (Mot de passe):</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
        </div>

        <div class="form-group">
            <label for="mot_de_passe_confirm">Подтверждение Пароля:</label>
            <input type="password" id="mot_de_passe_confirm" name="mot_de_passe_confirm" required>
        </div>
        
        <hr>
        
        <div class="form-group privacy-checkbox">
            <input type="checkbox" id="privacy_policy" name="privacy_policy" required>
            <label for="privacy_policy">Я согласен с <a href="#" target="_blank">Политикой конфиденциальности</a> и условиями использования.</label>
        </div>
        
        <button type="submit" class="btn btn-primary">Зарегистрироваться (S'inscrire)</button>
    </form>
    
    <p class="link-to-login">
        Уже есть аккаунт? <a href="?route=login">Войдите (Connexion)</a>
    </p>

</div>