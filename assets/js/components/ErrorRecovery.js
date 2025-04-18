import { VoteService } from '../services/VoteService.js';
import { ErrorHandler } from './ErrorHandler.js';
import { LoadingHandler } from './LoadingHandler.js';

export class ErrorRecovery {
    constructor() {
        this.errorHandler = new ErrorHandler();
        this.loadingHandler = new LoadingHandler();
        this.maxRetries = 3;
        this.retryDelay = 1000;
        this.initializeRecovery();
    }

    initializeRecovery() {
        // Listen for offline/online events
        window.addEventListener('offline', () => this.handleOffline());
        window.addEventListener('online', () => this.handleOnline());

        // Initialize service worker for offline support
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    console.log('ServiceWorker registration successful');
                })
                .catch(error => {
                    console.error('ServiceWorker registration failed:', error);
                });
        }
    }

    handleOffline() {
        this.errorHandler.showError('You are offline. Some features may be unavailable.');
    }

    handleOnline() {
        this.errorHandler.showSuccess('You are back online!');
        this.retryFailedOperations();
    }

    async retryFailedOperations() {
        const failedOperations = JSON.parse(localStorage.getItem('failedOperations') || '[]');
        
        if (failedOperations.length > 0) {
            this.loadingHandler.show();
            
            for (const operation of failedOperations) {
                try {
                    await this.retryOperation(operation);
                    // Remove successful operation
                    failedOperations.splice(failedOperations.indexOf(operation), 1);
                    localStorage.setItem('failedOperations', JSON.stringify(failedOperations));
                } catch (error) {
                    console.error('Failed to retry operation:', error);
                }
            }
            
            this.loadingHandler.hide();
        }
    }

    async retryOperation(operation, retryCount = 0) {
        try {
            switch (operation.type) {
                case 'vote':
                    await VoteService.submitVotes(operation.data);
                    break;
                case 'status':
                    await VoteService.getVotingStatus();
                    break;
                default:
                    throw new Error('Unknown operation type');
            }
        } catch (error) {
            if (retryCount < this.maxRetries) {
                await new Promise(resolve => setTimeout(resolve, this.retryDelay));
                return this.retryOperation(operation, retryCount + 1);
            }
            throw error;
        }
    }

    storeFailedOperation(operation) {
        const failedOperations = JSON.parse(localStorage.getItem('failedOperations') || '[]');
        failedOperations.push(operation);
        localStorage.setItem('failedOperations', JSON.stringify(failedOperations));
    }

    clearFailedOperations() {
        localStorage.removeItem('failedOperations');
    }
} 