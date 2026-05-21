document.addEventListener("DOMContentLoaded", () => {
    const registerForm = document.getElementById("register-form");

    if (registerForm) {
        registerForm.addEventListener("submit", (event) => {
            event.preventDefault();

            const nameInput = document.getElementById("name");
            const emailInput = document.getElementById("email");
            const passwordInput = document.getElementById("password");
            const confirmPasswordInput = document.getElementById("confirm-password");

            const name = nameInput.value.trim();
            const email = emailInput.value.trim();
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            clearRegisterErrors();

            let isValid = true;

            if (name === "") {
                showInputError("name", "name-error", "Unesi ime i prezime.");
                isValid = false;
            }

            if (email === "") {
                showInputError("email", "email-error", "Unesi e-mail adresu.");
                isValid = false;
            } else if (!isValidEmail(email)) {
                showInputError("email", "email-error", "Unesi ispravnu e-mail adresu.");
                isValid = false;
            }

            if (password === "") {
                showInputError("password", "password-error", "Unesi lozinku.");
                isValid = false;
            } else if (password.length < 6) {
                showInputError("password", "password-error", "Lozinka mora imati najmanje 6 znakova.");
                isValid = false;
            }

            if (confirmPassword === "") {
                showInputError(
                    "confirm-password",
                    "confirm-password-error",
                    "Potvrdi lozinku."
                );
                isValid = false;
            } else if (password !== confirmPassword) {
                showInputError(
                    "confirm-password",
                    "confirm-password-error",
                    "Lozinke se ne podudaraju."
                );
                isValid = false;
            }

            if (isValid) {
                registerUser({
                    name,
                    email,
                    password,
                    confirmPassword
                });
            }
        });
    }


    const loginForm = document.getElementById("login-form");

    if (loginForm) {
        loginForm.addEventListener("submit", (event) => {
            event.preventDefault();

            const emailInput = document.getElementById("login-email");
            const passwordInput = document.getElementById("login-password");

            const email = emailInput.value.trim();
            const password = passwordInput.value;

            clearLoginErrors();

            let isValid = true;

            if (email === "") {
                showInputError(
                    "login-email",
                    "login-email-error",
                    "Unesi e-mail adresu."
                );
                isValid = false;
            } else if (!isValidEmail(email)) {
                showInputError(
                    "login-email",
                    "login-email-error",
                    "Unesi ispravnu e-mail adresu."
                );
                isValid = false;
            }

            if (password === "") {
                showInputError(
                    "login-password",
                    "login-password-error",
                    "Unesi lozinku."
                );
                isValid = false;
            }

            if (isValid) {
                loginUser({
                    email,
                    password
                });
            }
        });
    }
});

function isValidEmail(email) {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailPattern.test(email);
}

function showInputError(inputId, errorId, message) {
    const input = document.getElementById(inputId);
    const error = document.getElementById(errorId);

    input.classList.add("input-error");
    error.textContent = message;
}

function clearRegisterErrors() {
    const errorElements = document.querySelectorAll(".error-message");
    const inputElements = document.querySelectorAll(".auth-form input");

    errorElements.forEach((element) => {
        element.textContent = "";
    });

    inputElements.forEach((input) => {
        input.classList.remove("input-error");
    });

    const registerMessage = document.getElementById("register-message");
    registerMessage.className = "form-message";
    registerMessage.textContent = "";
}

function showFormMessage(elementId, type, message) {
    const messageBox = document.getElementById(elementId);
    messageBox.className = `form-message ${type}`;
    messageBox.textContent = message;
}


async function registerUser(userData) {
    try {
        const response = await fetch("/carcare/api/auth/register.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(userData)
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            showFormMessage("register-message", "error", result.message);
            return;
        }

        showFormMessage("register-message", "success", result.message);

        document.getElementById("register-form").reset();

        setTimeout(() => {
            window.location.href = "/carcare/login.php";
        }, 1800);

    } catch (error) {
        showFormMessage(
            "register-message",
            "error",
            "Nije moguće poslati zahtjev. Pokušaj ponovno."
        );
    }
}


function clearLoginErrors() {
    const loginEmailError = document.getElementById("login-email-error");
    const loginPasswordError = document.getElementById("login-password-error");

    const loginEmailInput = document.getElementById("login-email");
    const loginPasswordInput = document.getElementById("login-password");

    loginEmailError.textContent = "";
    loginPasswordError.textContent = "";

    loginEmailInput.classList.remove("input-error");
    loginPasswordInput.classList.remove("input-error");

    const loginMessage = document.getElementById("login-message");
    loginMessage.className = "form-message";
    loginMessage.textContent = "";
}


async function loginUser(loginData) {
    try {
        const response = await fetch("/carcare/api/auth/login.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(loginData)
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            showFormMessage("login-message", "error", result.message);
            return;
        }

        showFormMessage("login-message", "success", result.message);

        setTimeout(() => {
            window.location.href = result.redirect;
        }, 1000);

    } catch (error) {
        showFormMessage(
            "login-message",
            "error",
            "Nije moguće poslati zahtjev. Pokušaj ponovno."
        );
    }
}