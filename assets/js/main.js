// Main JavaScript file for the voting system

// Common utility functions
const Utils = {
    // Show loading state
    showLoading: (element) => {
        element.disabled = true;
        const originalHTML = element.innerHTML;
        element.setAttribute('data-original-html', originalHTML);
        element.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing...';
    },

    // Reset loading state
    resetLoading: (element) => {
        element.disabled = false;
        const originalHTML = element.getAttribute('data-original-html');
        if (originalHTML) {
            element.innerHTML = originalHTML;
        }
    },

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
        Utils.showAlert('An error occurred. Please try again.');
        if (element) {
            Utils.resetLoading(element);
        }
    },

    // Format date
    formatDate: (dateString) => {
        const options = { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return new Date(dateString).toLocaleDateString(undefined, options);
    }
};

// Export for use in other files
window.Utils = Utils;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Handle form submissions
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = 'Processing...';
            }
        });
    });

    // Handle vote confirmation
    const voteButtons = document.querySelectorAll('.vote-button');
    voteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to vote for this candidate?')) {
                e.preventDefault();
            }
        });
    });

    // Handle delete confirmation
    const deleteButtons = document.querySelectorAll('.delete-button');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease-in-out';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
}); 