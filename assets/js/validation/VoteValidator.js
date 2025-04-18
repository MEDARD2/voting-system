// Vote form validation
export class VoteValidator {
    static validateForm(form) {
        // Check if at least one vote is selected
        const selectedVotes = form.querySelectorAll('input[type="radio"]:checked');
        if (selectedVotes.length === 0) {
            return {
                isValid: false,
                message: 'Please select at least one candidate to vote.'
            };
        }

        // Check if all required positions have votes
        const requiredPositions = form.querySelectorAll('.position-title');
        const votedPositions = new Set();
        
        selectedVotes.forEach(vote => {
            votedPositions.add(vote.getAttribute('name'));
        });

        if (requiredPositions.length !== votedPositions.size) {
            return {
                isValid: false,
                message: 'Please vote for all required positions.'
            };
        }

        return {
            isValid: true,
            message: 'Form is valid'
        };
    }

    static validateVote(vote) {
        if (!vote || !vote.position || !vote.candidateId) {
            return {
                isValid: false,
                message: 'Invalid vote data'
            };
        }

        return {
            isValid: true,
            message: 'Vote is valid'
        };
    }

    static validatePositionSelection(positionGroup) {
        const selectedVotes = positionGroup.querySelectorAll('input[type="radio"]:checked');
        const maxWinners = parseInt(positionGroup.dataset.maxWinners || '1');
        
        if (selectedVotes.length > maxWinners) {
            return {
                isValid: false,
                message: `You can only select up to ${maxWinners} candidate(s) for this position.`
            };
        }
        
        return { isValid: true };
    }
} 