// Theme toggle handling
// Listen for theme-changed event from Livewire and update the html class immediately
document.addEventListener('livewire:initialized', () => {
    Livewire.on('theme-changed', (dark) => {
        const isDark = dark[0];
        if (isDark) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
            document.cookie = 'theme=dark; path=/; max-age=31536000';
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
            document.cookie = 'theme=light; path=/; max-age=31536000';
        }
    });
});

// Prevent FOUC: Set theme from localStorage before page renders
// This runs immediately before any paint
(function () {
    const theme = localStorage.getItem('theme');
    if (theme === 'light') {
        document.documentElement.classList.remove('dark');
    } else if (theme === 'dark') {
        document.documentElement.classList.add('dark');
    }
})();
