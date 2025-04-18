// Bio text handler component
export class BioHandler {
    constructor() {
        this.initializeBioButtons();
    }

    initializeBioButtons() {
        document.querySelectorAll('.show-more-btn').forEach(button => {
            button.addEventListener('click', () => {
                const bio = button.previousElementSibling;
                const isExpanded = bio.classList.toggle('expanded');
                button.textContent = isExpanded ? 'Show Less' : 'Show More';
            });
        });
    }
} 