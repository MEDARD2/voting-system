import { LoadingHandler } from '../../assets/js/components/LoadingHandler.js';

describe('LoadingHandler', () => {
    let handler;

    beforeEach(() => {
        document.body.innerHTML = '';
        handler = new LoadingHandler();
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    test('should create loading overlay if not exists', () => {
        const overlay = document.getElementById('loadingOverlay');
        expect(overlay).toBeTruthy();
        expect(overlay.querySelector('.loading-spinner')).toBeTruthy();
        expect(overlay.querySelector('.loading-text')).toBeTruthy();
    });

    test('should show loading overlay', () => {
        handler.show();
        
        const overlay = document.getElementById('loadingOverlay');
        expect(overlay.style.display).toBe('flex');
        expect(overlay.classList.contains('fade-in')).toBe(true);
    });

    test('should hide loading overlay', () => {
        handler.show();
        handler.hide();
        
        const overlay = document.getElementById('loadingOverlay');
        expect(overlay.classList.contains('fade-out')).toBe(true);
        
        // Wait for fade out animation
        setTimeout(() => {
            expect(overlay.style.display).toBe('none');
            expect(overlay.classList.contains('fade-out')).toBe(false);
        }, 300);
    });

    test('should handle multiple show/hide calls', () => {
        handler.show();
        handler.show();
        handler.hide();
        handler.hide();
        
        const overlay = document.getElementById('loadingOverlay');
        expect(overlay.classList.contains('fade-out')).toBe(true);
    });
}); 