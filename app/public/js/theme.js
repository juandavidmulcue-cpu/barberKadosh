document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const themeText = document.getElementById('theme-text');

    // Cargar el tema guardado o usar 'dark' por defecto
    const savedTheme = localStorage.getItem('theme') || 'dark';
    document.documentElement.setAttribute('data-theme', savedTheme);
    updateButton(savedTheme);

    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateButton(newTheme);
        });
    }

    function updateButton(theme) {
        if (!themeIcon || !themeText) return;
        if (theme === 'dark') {
            themeIcon.textContent = '☀️';
            themeText.textContent = 'Modo Claro';
        } else {
            themeIcon.textContent = '🌙';
            themeText.textContent = 'Modo Oscuro';
        }
    }
});