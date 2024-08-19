function validateForm() {
    let isValid = true;

    // Clear previous error messages
    const errorMessages = {
        email: "email-error",
        username: "username-error",
        password: "password-error",
        passwordMatch: "password-match",
        agreement: "agreement-error"
    };

    for (const key in errorMessages) {
        document.getElementById(errorMessages[key]).innerHTML = "";
    }

    // Validate email
    const email = document.getElementById("email").value.trim();
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if (!emailPattern.test(email)) {
        document.getElementById(errorMessages.email).innerHTML = "Невалиден имейл адрес!";
        isValid = false;
    }

    // Validate username
    const username = document.getElementById("user").value.trim();
    if (username.length < 5 || username.length > 20) {
        document.getElementById(errorMessages.username).innerHTML = "Потребителското име трябва да бъде между 5 и 20 символа!";
        isValid = false;
    }

    // Validate password
    const password = document.getElementById("pwd").value.trim();
    if (password.length < 8) {
        document.getElementById(errorMessages.password).innerHTML = "Паролата трябва да бъде поне 8 символа!";
        isValid = false;
    }

    // Validate password match
    const confirmPassword = document.getElementById("confirm-password").value.trim();
    if (password !== confirmPassword) {
        document.getElementById(errorMessages.passwordMatch).innerHTML = "Паролите не съвпадат!";
        isValid = false;
    }

    // Validate agreement checkbox
    const agreement = document.getElementById("agreement").checked;
    if (!agreement) {
        document.getElementById(errorMessages.agreement).innerHTML = "Трябва да се съгласите с условията и правилата!";
        isValid = false;
    }

    return isValid; // If all validations pass, submit the form
}

// Display server-side validation errors if any
document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const serverErrors = {
        email: urlParams.get('email'),
        user: urlParams.get('user')
    };

    if (serverErrors.email) {
        document.getElementById('email-error').innerHTML = serverErrors.email;
    }
    if (serverErrors.user) {
        document.getElementById('username-error').innerHTML = serverErrors.user;
    }
});