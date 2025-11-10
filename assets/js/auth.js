// assets/js/auth.js

document.addEventListener('DOMContentLoaded', function() {
    
    const toggleBtn = document.getElementById('toggle_owner_register');
    const codeField = document.getElementById('owner-code-field');
    const ownerSubmitBtn = document.getElementById('owner-register-btn');
    const clientSubmitBtn = document.querySelector('button[name="register_client"]');

    if (toggleBtn && codeField && ownerSubmitBtn && clientSubmitBtn) {
        
        toggleBtn.addEventListener('click', function() {
            
            // ИСПОЛЬЗУЕМ CSS-КЛАССЫ ВМЕСТО INLINE-СТИЛЕЙ
            
            // Показываем поле кода (убираем класс .hidden)
            codeField.classList.remove('hidden');
            
            // Показываем кнопку "Подтвердить Владельца"
            ownerSubmitBtn.classList.remove('hidden');
            
            // Скрываем кнопку "Клиент" (добавляем класс .hidden)
            clientSubmitBtn.classList.add('hidden');
            
            // Скрываем кнопку "Я - Владелец"
            toggleBtn.classList.add('hidden');
        });
    }
});