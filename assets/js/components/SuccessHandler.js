import { Animations } from '../ui/animations.js';

export class SuccessHandler {
    constructor() {
        this.successContainer = document.getElementById('successContainer');
        if (!this.successContainer) {
            this.successContainer = document.createElement('div');
            this.successContainer.id = 'successContainer';
            document.body.appendChild(this.successContainer);
        }
    }

    showSuccess(message, duration = 3000) {
        const successElement = document.createElement('div');
        successElement.className = 'success-message';
        successElement.innerHTML = `
            <div class="success-icon">✓</div>
            <div class="success-text">${message}</div>
        `;

        this.successContainer.appendChild(successElement);
        Animations.fadeIn(successElement);

        setTimeout(() => {
            successElement.classList.add('fade-out');
            setTimeout(() => {
                successElement.remove();
            }, 300);
        }, duration);
    }

    clearSuccess() {
        this.successContainer.innerHTML = '';
    }
} 