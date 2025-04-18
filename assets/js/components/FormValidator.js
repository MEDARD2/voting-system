import { Animations } from '../ui/animations.js';

export class FormValidator {
    constructor(form) {
        this.form = form;
        this.initializeValidation();
    }

    initializeValidation() {
        // Add error message containers for each input
        this.form.querySelectorAll('input[type="radio"]').forEach(input => {
            const errorContainer = document.createElement('div');
            errorContainer.className = 'form-error';
            errorContainer.setAttribute('aria-live', 'polite');
            input.parentNode.appendChild(errorContainer);
        });

        // Add validation on change
        this.form.querySelectorAll('input[type="radio"]').forEach(input => {
            input.addEventListener('change', () => {
                this.validateInput(input);
            });
        });

        // Add validation on form submit
        this.form.addEventListener('submit', (e) => {
            if (!this.validateForm()) {
                e.preventDefault();
            }
        });
    }

    validateInput(input) {
        const errorContainer = input.parentNode.querySelector('.form-error');
        const positionGroup = input.closest('.position-group');
        const selectedVotes = positionGroup.querySelectorAll('input[type="radio"]:checked');

        // Clear previous error
        errorContainer.textContent = '';
        errorContainer.classList.remove('show');
        input.classList.remove('input-error');

        // Validate selection
        if (selectedVotes.length > 1) {
            this.showError(input, 'You can only select one candidate per position');
            return false;
        }

        return true;
    }

    validateForm() {
        let isValid = true;
        const positionGroups = this.form.querySelectorAll('.position-group');

        positionGroups.forEach(group => {
            const selectedVotes = group.querySelectorAll('input[type="radio"]:checked');
            const errorContainer = group.querySelector('.form-error');

            if (selectedVotes.length === 0) {
                this.showError(group.querySelector('input[type="radio"]'), 'Please select a candidate for this position');
                isValid = false;
            }
        });

        return isValid;
    }

    showError(input, message) {
        const errorContainer = input.parentNode.querySelector('.form-error');
        errorContainer.textContent = message;
        errorContainer.classList.add('show');
        input.classList.add('input-error');
        Animations.pulse(errorContainer);
    }

    clearErrors() {
        this.form.querySelectorAll('.form-error').forEach(error => {
            error.textContent = '';
            error.classList.remove('show');
        });
        this.form.querySelectorAll('.input-error').forEach(input => {
            input.classList.remove('input-error');
        });
    }
} 