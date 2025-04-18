// Vote selection handler component
export class VoteSelection {
    constructor() {
        this.initializeSelectionHandlers();
    }

    initializeSelectionHandlers() {
        document.querySelectorAll('input[name^="votes["]').forEach(input => {
            input.addEventListener('change', () => {
                const card = input.closest('.candidate-card');
                if (input.checked) {
                    card.classList.add('selected');
                } else {
                    card.classList.remove('selected');
                }
            });
        });

        // Add fade-in animation to cards
        document.querySelectorAll('.candidate-card').forEach(card => {
            card.classList.add('fade-in');
        });
    }
} 