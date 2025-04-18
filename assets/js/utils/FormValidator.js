class FormValidator {
    static validateVoteForm() {
        const form = document.getElementById('voteForm');
        if (!form) {
            console.error('Vote form not found');
            return false;
        }

        // Check if at least one vote is selected
        const selectedVotes = form.querySelectorAll('input[type="radio"]:checked');
        if (selectedVotes.length === 0) {
            ErrorHandler.showError('Please select at least one candidate to vote');
            return false;
        }

        // Validate each selected vote
        for (const vote of selectedVotes) {
            const positionId = vote.getAttribute('data-position-id');
            const candidateId = vote.value;

            if (!positionId || !candidateId) {
                ErrorHandler.showError('Invalid vote selection detected');
                return false;
            }
        }

        return true;
    }

    static validateLoginForm() {
        const form = document.getElementById('loginForm');
        if (!form) {
            console.error('Login form not found');
            return false;
        }

        const username = form.querySelector('input[name="username"]')?.value.trim();
        const password = form.querySelector('input[name="password"]')?.value.trim();

        if (!username) {
            ErrorHandler.showError('Please enter your username');
            return false;
        }

        if (!password) {
            ErrorHandler.showError('Please enter your password');
            return false;
        }

        return true;
    }

    static validateRegistrationForm() {
        const form = document.getElementById('registrationForm');
        if (!form) {
            console.error('Registration form not found');
            return false;
        }

        const username = form.querySelector('input[name="username"]')?.value.trim();
        const password = form.querySelector('input[name="password"]')?.value.trim();
        const confirmPassword = form.querySelector('input[name="confirm_password"]')?.value.trim();
        const email = form.querySelector('input[name="email"]')?.value.trim();

        if (!username) {
            ErrorHandler.showError('Please enter a username');
            return false;
        }

        if (username.length < 3) {
            ErrorHandler.showError('Username must be at least 3 characters long');
            return false;
        }

        if (!password) {
            ErrorHandler.showError('Please enter a password');
            return false;
        }

        if (password.length < 6) {
            ErrorHandler.showError('Password must be at least 6 characters long');
            return false;
        }

        if (password !== confirmPassword) {
            ErrorHandler.showError('Passwords do not match');
            return false;
        }

        if (!email) {
            ErrorHandler.showError('Please enter your email');
            return false;
        }

        if (!this.isValidEmail(email)) {
            ErrorHandler.showError('Please enter a valid email address');
            return false;
        }

        return true;
    }

    static isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    static validateAdminForm(formId) {
        const form = document.getElementById(formId);
        if (!form) {
            console.error(`Form ${formId} not found`);
            return false;
        }

        // Common validation for admin forms
        const requiredFields = form.querySelectorAll('[required]');
        for (const field of requiredFields) {
            if (!field.value.trim()) {
                ErrorHandler.showError(`Please fill in the ${field.name} field`);
                field.focus();
                return false;
            }
        }

        // Specific validations based on form type
        switch (formId) {
            case 'addCandidateForm':
                return this.validateAddCandidateForm(form);
            case 'addPositionForm':
                return this.validateAddPositionForm(form);
            case 'updateSettingsForm':
                return this.validateUpdateSettingsForm(form);
            default:
                return true;
        }
    }

    static validateAddCandidateForm(form) {
        const name = form.querySelector('input[name="name"]')?.value.trim();
        const position = form.querySelector('select[name="position"]')?.value;
        const bio = form.querySelector('textarea[name="bio"]')?.value.trim();
        const image = form.querySelector('input[name="image"]')?.files[0];

        if (!name || name.length < 2) {
            ErrorHandler.showError('Please enter a valid candidate name');
            return false;
        }

        if (!position) {
            ErrorHandler.showError('Please select a position');
            return false;
        }

        if (!bio || bio.length < 10) {
            ErrorHandler.showError('Please enter a valid bio (at least 10 characters)');
            return false;
        }

        if (image) {
            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!validTypes.includes(image.type)) {
                ErrorHandler.showError('Please upload a valid image (JPEG, PNG, or GIF)');
                return false;
            }

            if (image.size > 5 * 1024 * 1024) { // 5MB limit
                ErrorHandler.showError('Image size should not exceed 5MB');
                return false;
            }
        }

        return true;
    }

    static validateAddPositionForm(form) {
        const title = form.querySelector('input[name="title"]')?.value.trim();
        const description = form.querySelector('textarea[name="description"]')?.value.trim();

        if (!title || title.length < 3) {
            ErrorHandler.showError('Please enter a valid position title');
            return false;
        }

        if (!description || description.length < 10) {
            ErrorHandler.showError('Please enter a valid description (at least 10 characters)');
            return false;
        }

        return true;
    }

    static validateUpdateSettingsForm(form) {
        const votingStart = form.querySelector('input[name="voting_start"]')?.value;
        const votingEnd = form.querySelector('input[name="voting_end"]')?.value;

        if (!votingStart || !votingEnd) {
            ErrorHandler.showError('Please select both start and end dates');
            return false;
        }

        const startDate = new Date(votingStart);
        const endDate = new Date(votingEnd);

        if (startDate >= endDate) {
            ErrorHandler.showError('End date must be after start date');
            return false;
        }

        return true;
    }
}

export default FormValidator; 