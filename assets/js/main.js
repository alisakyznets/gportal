document.addEventListener('DOMContentLoaded', function() {
    // Анимация для блока "до/после"
    const beforeAfterItems = document.querySelectorAll('.ba-item');
    if (beforeAfterItems.length > 0) {
        beforeAfterItems.forEach(item => {
            item.addEventListener('click', function() {
                this.classList.toggle('show-after');
            });
        });
    }
    
    // Подтверждение удаления
    const deleteButtons = document.querySelectorAll('.btn-danger');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Вы уверены, что хотите выполнить это действие?')) {
                e.preventDefault();
            }
        });
    });
    
    // Валидация форм
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let valid = true;
            const requiredFields = this.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.classList.add('invalid');
                } else {
                    field.classList.remove('invalid');
                }
            });
            
            if (!valid) {
                e.preventDefault();
                alert('Пожалуйста, заполните все обязательные поля');
            }
        });
    });
    
    // Анимация сообщений
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
    
    // Переключение видимости пароля
    const passwordToggles = document.querySelectorAll('.password-toggle');
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const passwordInput = this.previousElementSibling;
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.textContent = type === 'password' ? '👁️' : '👁️‍🗨️';
        });
    });
});