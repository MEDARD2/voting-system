// Date utility functions
export class DateUtils {
    static updateTimeRemaining(endTime) {
        const now = new Date();
        const end = new Date(endTime);
        const timeRemaining = end - now;

        if (timeRemaining <= 0) {
            document.getElementById('timeRemaining').textContent = 'Voting has ended';
            return;
        }

        const hours = Math.floor(timeRemaining / (1000 * 60 * 60));
        const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);

        document.getElementById('timeRemaining').textContent = 
            `Time remaining: ${hours}h ${minutes}m ${seconds}s`;
    }

    static formatDate(date) {
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
} 