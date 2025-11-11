<div class="container">
    <h1>Мой Профиль</h1>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (isset($user_data)): ?>
        <div class="form-container">
            <form action="?route=user/profile" method="POST">
                
                <div class="form-group">
                    <label for="prenom">Имя:</label>
                    <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user_data['prenom']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="nom">Фамилия:</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user_data['nom']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="telephone">Телефон:</label>
                    <input type="text" id="telephone" name="telephone" value="<?php echo htmlspecialchars($user_data['telephone'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required readonly>
                </div>

                <hr class="mt-20 mb-20">
                <p style="font-size: 0.9em; color: var(--color-secondary);">Оставьте поля пароля пустыми, если не хотите его менять.</p>

                <div class="form-group">
                    <label for="password">Новый Пароль:</label>
                    <input type="password" id="password" name="password">
                </div>

                <div class="form-group">
                    <label for="password_confirm">Повторите Пароль:</label>
                    <input type="password" id="password_confirm" name="password_confirm">
                </div>

                <button type="submit" class="btn btn-success">Сохранить Изменения</button>
            </form>
        </div>
    <?php else: ?>
        <p>Не удалось загрузить данные пользователя.</p>
    <?php endif; ?>
</div>