(function () {
    var form = document.getElementById('ev-reset-password-form');
    var feedback = document.getElementById('ev-reset-password-feedback');
    var devTokenFeedback = document.getElementById('ev-reset-password-dev-token');

    if (!form || !feedback) {
        return;
    }

    var forgotApiEndpoint = form.getAttribute('data-forgot-api-endpoint') || '';
    var resetApiEndpoint = form.getAttribute('data-reset-api-endpoint') || '';
    var loginUrl = form.getAttribute('data-login-url') || '/login';
    var initialToken = form.getAttribute('data-reset-token') || '';
    var submitButton = form.querySelector('button[type="submit"]');
    var defaultSubmitLabel = submitButton ? (submitButton.getAttribute('data-default-label') || submitButton.textContent) : '';

    var emailFieldWrapper = form.querySelector('.ev-reset-password-form__email');
    var tokenFieldWrapper = form.querySelector('.ev-reset-password-form__token');
    var newPasswordWrapper = form.querySelector('.ev-reset-password-form__new-password');
    var confirmPasswordWrapper = form.querySelector('.ev-reset-password-form__confirm-password');

    function setFeedback(message, type) {
        var hasMessage = !!message;

        feedback.hidden = !hasMessage;
        feedback.textContent = message || '';
        feedback.classList.remove('is-error', 'is-success', 'is-loading');

        if (!hasMessage) {
            return;
        }

        if (type === 'error') {
            feedback.classList.add('is-error');
        }

        if (type === 'success') {
            feedback.classList.add('is-success');
        }

        if (type === 'loading') {
            feedback.classList.add('is-loading');
        }
    }

    function setSubmitState(isLoading, loadingLabel) {
        if (!submitButton) {
            return;
        }

        submitButton.disabled = isLoading;
        submitButton.textContent = isLoading ? loadingLabel : defaultSubmitLabel;
    }

    function setDevTokenMessage(message) {
        if (!devTokenFeedback) {
            return;
        }

        devTokenFeedback.hidden = !message;
        devTokenFeedback.textContent = message || '';
    }

    function parseResponseBody(response) {
        return response.text().then(function (text) {
            if (!text) {
                return {};
            }

            try {
                return JSON.parse(text);
            } catch (error) {
                return {};
            }
        });
    }

    function updateMode(mode) {
        var isResetMode = mode === 'reset';

        if (emailFieldWrapper) {
            emailFieldWrapper.hidden = isResetMode;
        }

        if (tokenFieldWrapper) {
            tokenFieldWrapper.hidden = !isResetMode;
        }

        if (newPasswordWrapper) {
            newPasswordWrapper.hidden = !isResetMode;
        }

        if (confirmPasswordWrapper) {
            confirmPasswordWrapper.hidden = !isResetMode;
        }

        if (form.email) {
            form.email.required = !isResetMode;
        }

        if (form.token) {
            form.token.required = isResetMode;
            form.token.value = initialToken;
            form.token.readOnly = initialToken !== '';
        }

        if (form.newPassword) {
            form.newPassword.required = isResetMode;
        }

        if (form.confirmPassword) {
            form.confirmPassword.required = isResetMode;
        }

        defaultSubmitLabel = isResetMode ? 'Aggiorna password' : 'Invia link di recupero';

        if (submitButton) {
            submitButton.setAttribute('data-default-label', defaultSubmitLabel);
            submitButton.textContent = defaultSubmitLabel;
        }
    }

    function handleForgotPassword() {
        var email = form.email ? form.email.value.trim() : '';

        if (!email) {
            setFeedback('Inserisci l email associata al tuo account.', 'error');
            return;
        }

        if (!forgotApiEndpoint) {
            setFeedback('Endpoint API recupero password non configurato.', 'error');
            return;
        }

        setFeedback('Invio richiesta di recupero in corso...', 'loading');
        setDevTokenMessage('');
        setSubmitState(true, 'Invio in corso...');

        fetch(forgotApiEndpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email: email
            })
        })
            .then(function (response) {
                return parseResponseBody(response).then(function (payload) {
                    if (!response.ok) {
                        throw new Error('Richiesta di recupero non riuscita.');
                    }

                    return payload;
                });
            })
            .then(function (payload) {
                setFeedback('Se l email esiste, abbiamo avviato la procedura di recupero password.', 'success');

                if (payload && payload.resetToken) {
                    var exampleUrl = payload.resetUrl || (window.location.origin + window.location.pathname + '?token=' + encodeURIComponent(payload.resetToken));
                    setDevTokenMessage('Ambiente di sviluppo: token ' + payload.resetToken + ' | URL di esempio: ' + exampleUrl);
                }

                form.reset();
            })
            .catch(function () {
                setFeedback('Si e verificato un errore durante il recupero password. Riprova piu tardi.', 'error');
            })
            .finally(function () {
                setSubmitState(false);
            });
    }

    function handleResetPassword() {
        var token = form.token ? form.token.value.trim() : '';
        var newPassword = form.newPassword ? form.newPassword.value : '';
        var confirmPassword = form.confirmPassword ? form.confirmPassword.value : '';

        if (!token) {
            setFeedback('Token di reset mancante. Apri il link ricevuto o incollalo nel campo dedicato.', 'error');
            return;
        }

        if (!newPassword || !confirmPassword) {
            setFeedback('Compila entrambi i campi password.', 'error');
            return;
        }

        if (newPassword !== confirmPassword) {
            setFeedback('Le due password non coincidono.', 'error');
            return;
        }

        if (!resetApiEndpoint) {
            setFeedback('Endpoint API reset password non configurato.', 'error');
            return;
        }

        setFeedback('Aggiornamento password in corso...', 'loading');
        setDevTokenMessage('');
        setSubmitState(true, 'Aggiornamento in corso...');

        fetch(resetApiEndpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                token: token,
                newPassword: newPassword
            })
        })
            .then(function (response) {
                return parseResponseBody(response).then(function (payload) {
                    if (!response.ok) {
                        throw new Error('Reset password non riuscito.');
                    }

                    return payload;
                });
            })
            .then(function () {
                setFeedback('Password aggiornata con successo. Tra pochi secondi verrai reindirizzato al login.', 'success');
                form.reset();

                window.setTimeout(function () {
                    window.location.href = loginUrl;
                }, 1800);
            })
            .catch(function () {
                setFeedback('Si e verificato un errore durante il reset della password. Riprova piu tardi.', 'error');
            })
            .finally(function () {
                setSubmitState(false);
            });
    }

    updateMode(initialToken ? 'reset' : 'forgot');

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        if (initialToken) {
            handleResetPassword();
            return;
        }

        handleForgotPassword();
    });
})();
