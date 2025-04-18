class ErrorHandler {
    static showError(message, duration = 5000) {
        // Create error container if it doesn't exist
        let errorContainer = document.getElementById('error-container');
        if (!errorContainer) {
            errorContainer = document.createElement('div');
            errorContainer.id = 'error-container';
            errorContainer.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                max-width: 400px;
            `;
            document.body.appendChild(errorContainer);
        }

        // Create error message element
        const errorElement = document.createElement('div');
        errorElement.className = 'error-message';
        errorElement.style.cssText = `
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideIn 0.3s ease-out;
        `;

        // Add message text
        const messageText = document.createElement('span');
        messageText.textContent = message;
        errorElement.appendChild(messageText);

        // Add close button
        const closeButton = document.createElement('button');
        closeButton.textContent = '×';
        closeButton.style.cssText = `
            background: none;
            border: none;
            color: #c62828;
            font-size: 20px;
            cursor: pointer;
            padding: 0 5px;
        `;
        closeButton.onclick = () => errorElement.remove();
        errorElement.appendChild(closeButton);

        // Add to container
        errorContainer.appendChild(errorElement);

        // Auto-remove after duration
        if (duration > 0) {
            setTimeout(() => {
                errorElement.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => errorElement.remove(), 300);
            }, duration);
        }

        // Add keyframes for animations
        if (!document.getElementById('error-animations')) {
            const style = document.createElement('style');
            style.id = 'error-animations';
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOut {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }
    }

    static showSuccess(message, duration = 5000) {
        // Create success container if it doesn't exist
        let successContainer = document.getElementById('success-container');
        if (!successContainer) {
            successContainer = document.createElement('div');
            successContainer.id = 'success-container';
            successContainer.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                max-width: 400px;
            `;
            document.body.appendChild(successContainer);
        }

        // Create success message element
        const successElement = document.createElement('div');
        successElement.className = 'success-message';
        successElement.style.cssText = `
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideIn 0.3s ease-out;
        `;

        // Add message text
        const messageText = document.createElement('span');
        messageText.textContent = message;
        successElement.appendChild(messageText);

        // Add close button
        const closeButton = document.createElement('button');
        closeButton.textContent = '×';
        closeButton.style.cssText = `
            background: none;
            border: none;
            color: #2e7d32;
            font-size: 20px;
            cursor: pointer;
            padding: 0 5px;
        `;
        closeButton.onclick = () => successElement.remove();
        successElement.appendChild(closeButton);

        // Add to container
        successContainer.appendChild(successElement);

        // Auto-remove after duration
        if (duration > 0) {
            setTimeout(() => {
                successElement.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => successElement.remove(), 300);
            }, duration);
        }
    }

    static handleApiError(error) {
        console.error('API Error:', error);
        let errorMessage = 'An error occurred while processing your request.';
        
        if (error.response) {
            // Server responded with an error status
            errorMessage = error.response.data?.message || errorMessage;
        } else if (error.request) {
            // Request was made but no response received
            errorMessage = 'No response from server. Please check your connection.';
        }
        
        this.showError(errorMessage);
        return errorMessage;
    }
}

export default ErrorHandler; 