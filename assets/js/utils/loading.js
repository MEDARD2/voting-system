// Loading state utility functions
export const LoadingUtils = {
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
    }
}; 