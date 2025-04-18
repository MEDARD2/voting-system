// Import components and utilities
import { BioHandler } from '../components/BioHandler.js';
import { VoteSelection } from '../components/VoteSelection.js';
import { VoteSubmission } from '../components/VoteSubmission.js';
import { ErrorHandler } from '../components/ErrorHandler.js';
import { LoadingHandler } from '../components/LoadingHandler.js';
import { SuccessHandler } from '../components/SuccessHandler.js';
import { FormValidator } from '../components/FormValidator.js';
import { DateUtils } from '../utils/date.js';
import { VoteService } from '../services/VoteService.js';
import { Animations } from '../ui/animations.js';

export class VoteHandler {
    constructor() {
        this.initializeComponents();
        this.initializeTimeRemaining();
        this.testApiConnection();
    }

    initializeComponents() {
        // Initialize UI components
        this.bioHandler = new BioHandler();
        this.voteSelection = new VoteSelection();
        this.voteSubmission = new VoteSubmission();
        this.errorHandler = new ErrorHandler();
        this.loadingHandler = new LoadingHandler();
        this.successHandler = new SuccessHandler();
        this.formValidator = new FormValidator(document.getElementById('votingForm'));

        // Add fade-in animation to all candidate cards
        document.querySelectorAll('.candidate-card').forEach(card => {
            Animations.fadeIn(card);
        });
    }

    initializeTimeRemaining() {
        const endTimeElement = document.getElementById('endTime');
        if (endTimeElement) {
            const endTime = endTimeElement.value;
            DateUtils.updateTimeRemaining(endTime);
            setInterval(() => DateUtils.updateTimeRemaining(endTime), 1000);
        }
    }

    async testApiConnection() {
        try {
            this.loadingHandler.show();
            await VoteService.testConnection();
            this.successHandler.showSuccess('Connected to server');
        } catch (error) {
            this.errorHandler.showError('Failed to connect to server');
            console.error('API connection test failed:', error);
        } finally {
            this.loadingHandler.hide();
        }
    }
} 