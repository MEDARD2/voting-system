import { Animations } from '../ui/animations.js';

export class LoadingHandler {
    constructor() {
        this.loadingOverlay = document.getElementById('loadingOverlay');
        if (!this.loadingOverlay) {
            this.loadingOverlay = document.createElement('div');
            this.loadingOverlay.id = 'loadingOverlay';
            this.loadingOverlay.className = 'loading-overlay';
            this.loadingOverlay.innerHTML = `
                <div class="loading-spinner"></div>
                <div class="loading-text">Loading...</div>
            `;
            document.body.appendChild(this.loadingOverlay);
        }
    }

    show() {
        this.loadingOverlay.style.display = 'flex';
        Animations.fadeIn(this.loadingOverlay);
    }

    hide() {
        this.loadingOverlay.classList.add('fade-out');
        setTimeout(() => {
            this.loadingOverlay.style.display = 'none';
            this.loadingOverlay.classList.remove('fade-out');
        }, 300);
    }
} 