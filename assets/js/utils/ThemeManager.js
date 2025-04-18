export class ThemeManager {
    constructor() {
        this.themes = {
            light: {
                '--primary-color': '#3498db',
                '--secondary-color': '#2980b9',
                '--background-color': '#ffffff',
                '--text-color': '#333333',
                '--card-background': '#f8f9fa',
                '--border-color': '#dee2e6',
                '--success-color': '#2ecc71',
                '--error-color': '#e74c3c',
                '--warning-color': '#f39c12'
            },
            dark: {
                '--primary-color': '#3498db',
                '--secondary-color': '#2980b9',
                '--background-color': '#1a1a1a',
                '--text-color': '#ffffff',
                '--card-background': '#2d2d2d',
                '--border-color': '#404040',
                '--success-color': '#27ae60',
                '--error-color': '#c0392b',
                '--warning-color': '#d35400'
            }
        };
        this.currentTheme = localStorage.getItem('theme') || 'light';
        this.initializeTheme();
    }

    initializeTheme() {
        this.applyTheme(this.currentTheme);
        this.addThemeToggle();
    }

    applyTheme(themeName) {
        const theme = this.themes[themeName];
        if (!theme) return;

        Object.entries(theme).forEach(([property, value]) => {
            document.documentElement.style.setProperty(property, value);
        });

        localStorage.setItem('theme', themeName);
        this.currentTheme = themeName;
    }

    toggleTheme() {
        const newTheme = this.currentTheme === 'light' ? 'dark' : 'light';
        this.applyTheme(newTheme);
    }

    addThemeToggle() {
        const toggle = document.createElement('button');
        toggle.className = 'theme-toggle';
        toggle.innerHTML = this.currentTheme === 'light' ? '🌙' : '☀️';
        toggle.onclick = () => this.toggleTheme();
        
        document.body.appendChild(toggle);
    }
} 