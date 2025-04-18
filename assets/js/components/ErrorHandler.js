import { Animations } from '../ui/animations.js';

export class ErrorHandler {
    constructor() {
        this.errorContainer = document.getElementById('errorContainer');
        if (!this.errorContainer) {
            this.errorContainer = document.createElement('div');
            this.errorContainer.id = 'errorContainer';
            document.body.appendChild(this.errorContainer);
        }
    }

    showError(message, duration = 5000) {
        const errorElement = document.createElement('div');
        errorElement.className = 'error-message';
        errorElement.textContent = message;

        this.errorContainer.appendChild(errorElement);
        Animations.fadeIn(errorElement);

        setTimeout(() => {
            errorElement.classList.add('fade-out');
            setTimeout(() => {
                errorElement.remove();
            }, 300);
        }, duration);
    }

    clearErrors() {
        this.errorContainer.innerHTML = '';
    }
} 