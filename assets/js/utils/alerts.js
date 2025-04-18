// Alert utility functions
export const AlertUtils = {
    // Show alert message
    showAlert: (message, type = 'danger') => {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        const container = document.querySelector('.container') || document.body;
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    },

    // Handle API errors
    handleApiError: (error, element = null) => {
        console.error('API Error:', error);
        AlertUtils.showAlert('An error occurred. Please try again.');
        if (element) {
            LoadingUtils.resetLoading(element);
        }
    }
}; 