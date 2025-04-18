import { VoteService } from '../services/VoteService.js';
import { VoteValidator } from '../validation/VoteValidator.js';
import { Animations } from '../ui/animations.js';

export class VoteSubmission {
    constructor() {
        this.form = document.querySelector('form');
        this.submitButton = document.querySelector('button[type="submit"]');
        this.initializeFormSubmission();
    }

    initializeFormSubmission() {
        if (!this.form) return;

        this.form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Validate form
            const validationResult = VoteValidator.validateForm(this.form);
            if (!validationResult.isValid) {
                alert(validationResult.message);
                return;
            }

            // Get selected votes
            const votes = this.getSelectedVotes();
            
            try {
                // Disable submit button and show loading state
                this.submitButton.disabled = true;
                this.submitButton.textContent = 'Submitting...';
                Animations.pulse(this.submitButton);

                // Submit votes
                const response = await VoteService.submitVotes(votes);
                
                if (response.success) {
                    // Show success animation and redirect
                    Animations.slideIn(document.querySelector('.success-message'));
                    setTimeout(() => {
                        window.location.href = 'results.php';
                    }, 1000);
                } else {
                    throw new Error(response.message || 'Failed to submit votes');
                }
            } catch (error) {
                console.error('Error submitting votes:', error);
                alert('Failed to submit votes. Please try again.');
                this.submitButton.disabled = false;
                this.submitButton.textContent = 'Submit Vote';
            }
        });
    }

    getSelectedVotes() {
        const votes = {};
        document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
            const position = radio.getAttribute('name');
            const candidateId = radio.value;
            if (!votes[position]) {
                votes[position] = [];
            }
            votes[position].push(candidateId);
        });
        return votes;
    }
} 