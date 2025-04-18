// Import the main VoteHandler
import { VoteHandler } from './core/VoteHandler.js';
import FormValidator from './utils/FormValidator.js';
import ErrorHandler from './utils/ErrorHandler.js';
import ThemeManager from './utils/ThemeManager.js';
import LocalizationManager from './utils/LocalizationManager.js';

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new VoteHandler();

    // Initialize managers
    const themeManager = new ThemeManager();
    const localizationManager = new LocalizationManager();

    // Get DOM elements
    const form = document.getElementById('voteForm');
    const submitButton = document.getElementById('submitVote');
    const timeRemainingElement = document.getElementById('timeRemaining');
    const endTime = document.getElementById('endTime')?.value;

    // Initialize time remaining counter
    if (timeRemainingElement && endTime) {
        updateTimeRemaining();
        setInterval(updateTimeRemaining, 1000);
    }

    // Add hover effects to candidate cards
    const candidateCards = document.querySelectorAll('.candidate-card');
    candidateCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-10px)';
            card.style.boxShadow = '0 15px 30px rgba(0,0,0,0.15)';
        });

        card.addEventListener('mouseleave', () => {
            if (!card.classList.contains('selected')) {
                card.style.transform = 'translateY(0)';
                card.style.boxShadow = '0 4px 20px rgba(0,0,0,0.1)';
            }
        });
    });

    // Handle radio button selection
    const radioButtons = document.querySelectorAll('input[type="radio"]');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            const card = this.closest('.candidate-card');
            const positionId = this.name;
            
            // Remove selection from other cards in the same position
            document.querySelectorAll(`input[name="${positionId}"]`).forEach(otherRadio => {
                const otherCard = otherRadio.closest('.candidate-card');
                if (otherCard !== card) {
                    otherCard.classList.remove('selected');
                    otherCard.style.transform = 'translateY(0)';
                    otherCard.style.boxShadow = '0 4px 20px rgba(0,0,0,0.1)';
                }
            });

            // Add selection to current card
            if (this.checked) {
                card.classList.add('selected');
                card.style.transform = 'translateY(-10px)';
                card.style.boxShadow = '0 15px 30px rgba(0,0,0,0.15)';
                
                // Add selection animation
                const selectionIndicator = card.querySelector('.selection-indicator');
                if (selectionIndicator) {
                    selectionIndicator.style.animation = 'pulse 0.5s ease';
                }
            }
        });
    });

    // Handle form submission
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!FormValidator.validateVoteForm()) {
                return;
            }

            // Disable form and show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <span class="loading"></span>
                ${localizationManager.getTranslation('vote.processing')}
            `;

            try {
                const formData = new FormData(form);
                const votes = {};

                // Convert FormData to object
                for (let [key, value] of formData.entries()) {
                    if (key.startsWith('votes[')) {
                        const positionId = key.match(/\[(.*?)\]/)[1];
                        votes[positionId] = value;
                    }
                }

                // Send vote to server
                const response = await fetch('vote.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ votes })
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const result = await response.json();

                if (result.success) {
                    // Show success message
                    ErrorHandler.showSuccess(localizationManager.getTranslation('vote.success'));
                    
                    // Redirect to results page
                    setTimeout(() => {
                        window.location.href = 'results.php';
                    }, 2000);
                } else {
                    throw new Error(result.message || 'Failed to submit vote');
                }
            } catch (error) {
                ErrorHandler.showError(error.message);
                submitButton.disabled = false;
                submitButton.innerHTML = localizationManager.getTranslation('vote.submit');
            }
        });
    }

    // Handle bio text toggle
    const showMoreButtons = document.querySelectorAll('.show-more-btn');
    showMoreButtons.forEach(button => {
        button.addEventListener('click', function() {
            const bio = this.previousElementSibling;
            if (bio.classList.contains('expanded')) {
                bio.classList.remove('expanded');
                this.textContent = localizationManager.getTranslation('candidate.showMore');
            } else {
                bio.classList.add('expanded');
                this.textContent = localizationManager.getTranslation('candidate.showLess');
            }
        });
    });

    // Add smooth scroll to position sections
    const positionSections = document.querySelectorAll('.position-section');
    positionSections.forEach(section => {
        section.addEventListener('click', function() {
            this.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    // Add parallax effect to header
    const header = document.querySelector('.voting-header');
    if (header) {
        window.addEventListener('scroll', () => {
            const scrollPosition = window.scrollY;
            header.style.backgroundPositionY = scrollPosition * 0.5 + 'px';
        });
    }

    // Handle vote button click
    const voteButtons = document.querySelectorAll('.vote-button');
    voteButtons.forEach(button => {
        button.addEventListener('click', () => {
            const candidateId = button.dataset.candidateId;
            const positionId = button.dataset.positionId;
            const input = document.querySelector(`input[name="votes[${positionId}]"]`);

            if (input) {
                input.value = candidateId;

                // Update all buttons for the same position
                voteButtons.forEach(btn => {
                    if (btn.dataset.positionId === positionId) {
                        if (btn === button) {
                            btn.innerHTML = '<i class="bi bi-check-circle"></i> Voted';
                            btn.classList.add('selected');
                            btn.classList.remove('not-selected');
                        } else {
                            btn.innerHTML = '<i class="bi bi-x-circle"></i> Not Selected';
                            btn.classList.add('not-selected');
                            btn.classList.remove('selected');
                        }
                    }
                });
            }
        });
    });

    form.addEventListener('submit', (event) => {
        const inputs = document.querySelectorAll('input[name^="votes"]');
        let hasVote = false;

        inputs.forEach(input => {
            if (input.value) {
                hasVote = true;
            }
        });

        if (!hasVote) {
            event.preventDefault();
            alert('Please vote for at least one position before submitting.');
        }
    });
});

// Update time remaining
function updateTimeRemaining() {
    const timeRemainingElement = document.getElementById('timeRemaining');
    const endTime = document.getElementById('endTime')?.value;
    
    if (!timeRemainingElement || !endTime) return;

    const now = new Date().getTime();
    const end = new Date(endTime).getTime();
    const distance = end - now;

    if (distance < 0) {
        timeRemainingElement.innerHTML = 'Voting has ended';
        return;
    }

    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    timeRemainingElement.innerHTML = `
        <span class="time-block">${days}d</span>
        <span class="time-block">${hours}h</span>
        <span class="time-block">${minutes}m</span>
        <span class="time-block">${seconds}s</span>
    `;
}