(function () {
    var form = document.getElementById('ev-register-form');
    var feedback = document.getElementById('ev-register-feedback');
    if (!form || !feedback) {
        return;
    }

    var apiEndpoint = form.getAttribute('data-api-endpoint') || '';

    function setFeedback(message, type) {
        var hasMessage = !!message;

        feedback.hidden = !hasMessage;
        feedback.textContent = message || '';
        feedback.classList.remove('is-error', 'is-success', 'is-loading');

        if (hasMessage && type) {
            feedback.classList.add(type);
        }
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        var firstName = form.firstName ? form.firstName.value.trim() : '';
        var lastName = form.lastName ? form.lastName.value.trim() : '';
        var email = form.email ? form.email.value.trim() : '';
        var password = form.password ? form.password.value : '';

        // Debug
        // console.log(fristName);
        // console.log(lastName);
        // console.log(email);
        // console.log(password);

        if (!firstName || !lastName || !email || !password) {
            setFeedback('Compila tutti i campi.', 'is-error');
            return;
        }

        if (!apiEndpoint) {
            setFeedback('Endpoint API registrazione non configurato.', 'is-error');
            return;
        }

        setFeedback('Creazione account in corso...', 'is-loading');

        fetch(apiEndpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                firstName: firstName,
                lastName: lastName,
                email: email,
                password: password
            })
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Registrazione non riuscita');
                }
                return response.json();
            })
            .then(function () {
                setFeedback('Registrazione completata con successo.', 'is-success');
                form.reset();
            })
            .catch(function (error) {
                setFeedback(error && error.message ? error.message : 'Errore durante la registrazione.', 'is-error');
            });
    });
})();
