export class LocalizationManager {
    constructor() {
        this.translations = {
            en: {
                'vote.submit': 'Submit Vote',
                'vote.processing': 'Processing...',
                'vote.success': 'Vote submitted successfully!',
                'vote.error': 'Error submitting vote',
                'vote.select': 'Please select at least one candidate',
                'candidate.bio': 'Bio',
                'candidate.showMore': 'Show More',
                'candidate.showLess': 'Show Less',
                'error.offline': 'You are offline. Some features may be unavailable.',
                'error.online': 'You are back online!',
                'error.retry': 'Retry Connection'
            },
            es: {
                'vote.submit': 'Enviar Voto',
                'vote.processing': 'Procesando...',
                'vote.success': '¡Voto enviado con éxito!',
                'vote.error': 'Error al enviar el voto',
                'vote.select': 'Por favor seleccione al menos un candidato',
                'candidate.bio': 'Biografía',
                'candidate.showMore': 'Mostrar Más',
                'candidate.showLess': 'Mostrar Menos',
                'error.offline': 'Estás sin conexión. Algunas funciones pueden no estar disponibles.',
                'error.online': '¡Estás de vuelta en línea!',
                'error.retry': 'Reintentar Conexión'
            }
        };
        this.currentLanguage = localStorage.getItem('language') || 'en';
        this.initializeLanguage();
    }

    initializeLanguage() {
        this.applyLanguage(this.currentLanguage);
        this.addLanguageSelector();
    }

    applyLanguage(language) {
        if (!this.translations[language]) return;

        document.querySelectorAll('[data-i18n]').forEach(element => {
            const key = element.getAttribute('data-i18n');
            if (this.translations[language][key]) {
                element.textContent = this.translations[language][key];
            }
        });

        localStorage.setItem('language', language);
        this.currentLanguage = language;
    }

    getTranslation(key) {
        return this.translations[this.currentLanguage][key] || key;
    }

    addLanguageSelector() {
        const selector = document.createElement('select');
        selector.className = 'language-selector';
        
        Object.keys(this.translations).forEach(lang => {
            const option = document.createElement('option');
            option.value = lang;
            option.textContent = lang.toUpperCase();
            option.selected = lang === this.currentLanguage;
            selector.appendChild(option);
        });

        selector.onchange = (e) => this.applyLanguage(e.target.value);
        document.body.appendChild(selector);
    }
} 