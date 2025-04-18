// Vote API service
export class VoteService {
    static async submitVotes(votes) {
        try {
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

            const data = await response.json();
            
            if (data.success) {
                return {
                    success: true,
                    message: data.message || 'Vote submitted successfully'
                };
            } else {
                throw new Error(data.message || 'Failed to submit vote');
            }
        } catch (error) {
            console.error('Error submitting votes:', error);
            return {
                success: false,
                message: error.message || 'An error occurred while submitting your vote'
            };
        }
    }

    static async getVotingStatus() {
        try {
            const response = await fetch('check_voting_status.php');
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            return {
                success: true,
                isOpen: data.isOpen,
                message: data.message
            };
        } catch (error) {
            console.error('Error checking voting status:', error);
            return {
                success: false,
                message: error.message || 'Failed to check voting status'
            };
        }
    }

    static async testConnection() {
        try {
            const response = await fetch('api/vote.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ test: true })
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error('API connection test failed:', error);
            throw error;
        }
    }
} 