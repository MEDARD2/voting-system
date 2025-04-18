import { ErrorHandler } from '../../assets/js/components/ErrorHandler.js';

describe('ErrorHandler', () => {
    let handler;

    beforeEach(() => {
        document.body.innerHTML = '';
        handler = new ErrorHandler();
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    test('should create error container if not exists', () => {
        const container = document.getElementById('errorContainer');
        expect(container).toBeTruthy();
    });

    test('should show error message', () => {
        handler.showError('Test error');
        
        const errorMessage = document.querySelector('.error-message');
        expect(errorMessage).toBeTruthy();
        expect(errorMessage.textContent).toBe('Test error');
        expect(errorMessage.classList.contains('fade-in')).toBe(true);
    });

    test('should clear errors', () => {
        handler.showError('Test error');
        handler.clearErrors();
        
        const container = document.getElementById('errorContainer');
        expect(container.innerHTML).toBe('');
    });

    test('should auto-dismiss error after duration', async () => {
        jest.useFakeTimers();
        
        handler.showError('Test error', 1000);
        
        const errorMessage = document.querySelector('.error-message');
        expect(errorMessage.classList.contains('fade-in')).toBe(true);
        
        jest.advanceTimersByTime(1000);
        
        expect(errorMessage.classList.contains('fade-out')).toBe(true);
        
        jest.advanceTimersByTime(300);
        
        expect(document.querySelector('.error-message')).toBeNull();
        
        jest.useRealTimers();
    });

    test('should handle multiple errors', () => {
        handler.showError('Error 1');
        handler.showError('Error 2');
        
        const errorMessages = document.querySelectorAll('.error-message');
        expect(errorMessages.length).toBe(2);
    });
}); 