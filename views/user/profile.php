<div class="container">
    <h1>Мой Профиль</h1>

    <?php if (isset($success)): ?>
        <p style="color: green; background-color: #e6ffe6; padding: 10px; border: 1px solid green; font-weight: bold;"><?php echo $success; ?></p>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <p style="color: red; background-color: #ffe6e6; padding: 10px; border: 1px solid red; font-weight: bold;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (isset($user_data)): ?>
        <form action="?route=user/profile" method="POST" style="max-width: 400px; margin: 20px 0;">
            
            <div style="margin-bottom: 15px;">
                <label for="prenom">Имя:</label>
                <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user_data['prenom']); ?>" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="nom">Фамилия:</label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user_data['nom']); ?>" required>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="telephone">Телефон:</label>
                <input type="text" id="telephone" name="telephone" value="<?php echo htmlspecialchars($user_data['telephone'] ?? ''); ?>">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
            </div>

            <hr style="margin: 20px 0;">
            <p style="font-size: 0.9em; color: gray;">Оставьте поля пароля пустыми, если не хотите его менять.</p>

            <div style="margin-bottom: 15px;">
                <label for="password">Новый Пароль:</label>
                <input type="password" id="password" name="password">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="password_confirm">Повторите Пароль:</label>
                <input type="password" id="password_confirm" name="password_confirm">
            </div>

            <button type="submit" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; cursor: pointer;">Сохранить Изменения</button>
        </form>
    <?php else: ?>
        <p>Не удалось загрузить данные пользователя.</p>
    <?php endif; ?>
</div>