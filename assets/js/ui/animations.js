// UI animations
export const Animations = {
    fadeIn: (element) => {
        element.classList.add('fade-in');
    },

    pulse: (element) => {
        element.style.animation = 'pulse 0.3s ease-in-out';
        setTimeout(() => {
            element.style.animation = '';
        }, 300);
    },

    slideIn: (element) => {
        element.classList.add('slide-in');
    }
}; 