(function () {
    var form = document.getElementById('ev-register-form');
    var feedback = document.getElementById('ev-register-feedback');
    if (!form || !feedback) {
        return;
    }

    var apiEndpoint = form.getAttribute('data-api-endpoint') || '';

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        var username = form.username ? form.username.value.trim() : '';
        var email = form.email ? form.email.value.trim() : '';
        var password = form.password ? form.password.value : '';

        if (!username || !email || !password) {
            feedback.textContent = 'Compila nome utente, email e password.';
            feedback.classList.add('is-error');
            return;
        }

        feedback.classList.remove('is-error', 'is-success');

        if (!apiEndpoint) {
            feedback.textContent = 'Endpoint API registrazione non configurato.';
            feedback.classList.add('is-error');
            return;
        }

        fetch(apiEndpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                username: username,
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
                feedback.textContent = 'Registrazione completata con successo.';
                feedback.classList.add('is-success');
                form.reset();
            })
            .catch(function (error) {
                feedback.textContent = error && error.message ? error.message : 'Errore durante la registrazione.';
                feedback.classList.add('is-error');
            });
    });
})();
