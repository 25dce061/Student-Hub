// ==============================================================================
// StudentHub - Student Registration Frontend Validation (js/register.js)
// Simple, beginner-friendly JavaScript validation for 2nd-semester practical
// ==============================================================================

document.addEventListener("DOMContentLoaded", function () {
    // 1. Get references to form elements
    const form = document.getElementById("registerForm");
    if (!form) return;

    const nameInput            = document.getElementById("name");
    const usernameInput        = document.getElementById("username");
    const emailInput           = document.getElementById("email");
    const passwordInput        = document.getElementById("password");
    const confirmPasswordInput = document.getElementById("confirmPassword");

    // 2. Get references to error message display spans
    const nameError            = document.getElementById("nameError");
    const usernameError        = document.getElementById("usernameError");
    const emailError           = document.getElementById("emailError");
    const passwordError        = document.getElementById("passwordError");
    const confirmPasswordError = document.getElementById("confirmPasswordError");

    // 3. Regular Expressions (as specified in syllabus/practical requirements)
    // Full Name: only letters and spaces, 3 to 30 characters
    const nameRegex = /^[A-Za-z ]{3,30}$/;

    // Username: letters and numbers, 3 to 15 characters
    const usernameRegex = /^[A-Za-z0-9]{3,15}$/;

    // Email: must be a valid Gmail address
    const emailRegex = /^[A-Za-z0-9._%+-]+@gmail\.com$/;

    // Password: at least 8 characters, at least 1 uppercase, 1 lowercase, and 1 number
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

    // ==========================================================================
    // Helper functions to show and clear error messages
    // ==========================================================================
    function showError(inputElement, errorElement, message) {
        errorElement.textContent = message;
        inputElement.classList.add("input-error");
        inputElement.classList.remove("input-success");
    }

    function clearError(inputElement, errorElement) {
        errorElement.textContent = "";
        inputElement.classList.remove("input-error");
        inputElement.classList.add("input-success");
    }

    // ==========================================================================
    // Field-by-Field Validation Functions
    // ==========================================================================

    // Validate Full Name
    function validateName() {
        const val = nameInput.value.trim();
        if (val === "") {
            showError(nameInput, nameError, "Full Name cannot be empty.");
            return false;
        }
        if (!nameRegex.test(val)) {
            showError(nameInput, nameError, "Only letters and spaces allowed (3 to 30 characters).");
            return false;
        }
        clearError(nameInput, nameError);
        return true;
    }

    // Validate Username
    function validateUsername() {
        const val = usernameInput.value.trim();
        if (val === "") {
            showError(usernameInput, usernameError, "Username cannot be empty.");
            return false;
        }
        if (!usernameRegex.test(val)) {
            showError(usernameInput, usernameError, "Username must be 3-15 characters (letters & numbers only).");
            return false;
        }
        clearError(usernameInput, usernameError);
        return true;
    }

    // Validate Email (Gmail only)
    function validateEmail() {
        const val = emailInput.value.trim();
        if (val === "") {
            showError(emailInput, emailError, "Email address cannot be empty.");
            return false;
        }
        if (!emailRegex.test(val)) {
            showError(emailInput, emailError, "Please enter a valid Gmail address (e.g., student@gmail.com).");
            return false;
        }
        clearError(emailInput, emailError);
        return true;
    }

    // Validate Password
    function validatePassword() {
        const val = passwordInput.value;
        if (val === "") {
            showError(passwordInput, passwordError, "Password cannot be empty.");
            return false;
        }
        if (!passwordRegex.test(val)) {
            showError(passwordInput, passwordError, "Min 8 chars, at least 1 uppercase, 1 lowercase, and 1 number.");
            return false;
        }
        clearError(passwordInput, passwordError);
        return true;
    }

    // Validate Confirm Password
    function validateConfirmPassword() {
        const pwd = passwordInput.value;
        const confirmPwd = confirmPasswordInput.value;
        if (confirmPwd === "") {
            showError(confirmPasswordInput, confirmPasswordError, "Please confirm your password.");
            return false;
        }
        if (pwd !== confirmPwd) {
            showError(confirmPasswordInput, confirmPasswordError, "Passwords do not match.");
            return false;
        }
        clearError(confirmPasswordInput, confirmPasswordError);
        return true;
    }

    // ==========================================================================
    // Real-Time Feedback (as the student types or leaves each field)
    // ==========================================================================
    nameInput.addEventListener("input", validateName);
    usernameInput.addEventListener("input", validateUsername);
    emailInput.addEventListener("input", validateEmail);

    passwordInput.addEventListener("input", function () {
        validatePassword();
        if (confirmPasswordInput.value.length > 0) {
            validateConfirmPassword();
        }
    });

    confirmPasswordInput.addEventListener("input", validateConfirmPassword);

    // ==========================================================================
    // Form Submit Event Handler
    // ==========================================================================
    form.addEventListener("submit", function (event) {
        // Run all individual validations
        const isNameValid       = validateName();
        const isUsernameValid   = validateUsername();
        const isEmailValid      = validateEmail();
        const isPasswordValid   = validatePassword();
        const isConfirmValid    = validateConfirmPassword();

        // If ANY field is invalid, cancel form submission
        if (!isNameValid || !isUsernameValid || !isEmailValid || !isPasswordValid || !isConfirmValid) {
            event.preventDefault(); // Stop the form from submitting
            // Focus on first invalid input
            if (!isNameValid) nameInput.focus();
            else if (!isUsernameValid) usernameInput.focus();
            else if (!isEmailValid) emailInput.focus();
            else if (!isPasswordValid) passwordInput.focus();
            else if (!isConfirmValid) confirmPasswordInput.focus();
        }
    });
});
