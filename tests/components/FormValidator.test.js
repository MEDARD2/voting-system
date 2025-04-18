import { FormValidator } from '../../assets/js/components/FormValidator.js';
import { createTestForm } from '../setup.js';

describe('FormValidator', () => {
    let form;
    let validator;

    beforeEach(() => {
        form = createTestForm();
        validator = new FormValidator(form);
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    test('should initialize error containers for each input', () => {
        const errorContainers = form.querySelectorAll('.form-error');
        expect(errorContainers.length).toBe(3);
    });

    test('should validate single selection per position', () => {
        const inputs = form.querySelectorAll('input[type="radio"]');
        
        // Select first input
        inputs[0].checked = true;
        inputs[0].dispatchEvent(new Event('change'));
        
        // Select second input
        inputs[1].checked = true;
        inputs[1].dispatchEvent(new Event('change'));
        
        const errorContainer = inputs[1].parentNode.querySelector('.form-error');
        expect(errorContainer.classList.contains('show')).toBe(true);
        expect(errorContainer.textContent).toBe('You can only select one candidate per position');
    });

    test('should validate form submission', () => {
        const submitEvent = new Event('submit');
        form.dispatchEvent(submitEvent);
        
        const errorContainer = form.querySelector('.form-error');
        expect(errorContainer.classList.contains('show')).toBe(true);
        expect(errorContainer.textContent).toBe('Please select a candidate for this position');
    });

    test('should clear errors', () => {
        const input = form.querySelector('input[type="radio"]');
        validator.showError(input, 'Test error');
        
        validator.clearErrors();
        
        const errorContainer = input.parentNode.querySelector('.form-error');
        expect(errorContainer.classList.contains('show')).toBe(false);
        expect(errorContainer.textContent).toBe('');
        expect(input.classList.contains('input-error')).toBe(false);
    });

    test('should prevent form submission when invalid', () => {
        const submitEvent = new Event('submit');
        const preventDefault = jest.spyOn(submitEvent, 'preventDefault');
        
        form.dispatchEvent(submitEvent);
        
        expect(preventDefault).toHaveBeenCalled();
    });
}); 