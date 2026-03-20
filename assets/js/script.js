// assets/js/script.js
// Validações opcionais no cliente, máscaras, etc.
document.addEventListener('DOMContentLoaded', function() {
    // Exemplo: máscara para telefone
    const telefoneInput = document.getElementById('telefone');
    if (telefoneInput) {
        telefoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 9) value = value.slice(0, 9);
            if (value.length > 0) {
                if (value.length <= 3) {
                    value = value.replace(/(\d{1,3})/, '($1');
                } else if (value.length <= 6) {
                    value = value.replace(/(\d{3})(\d{1,3})/, '($1) $2');
                } else {
                    value = value.replace(/(\d{3})(\d{3})(\d{1,3})/, '($1) $2-$3');
                }
            }
            e.target.value = value;
        });
    }

    // Auto-fechar alertas após 5 segundos
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});