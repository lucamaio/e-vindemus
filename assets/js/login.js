(function () {
    var form = document.getElementById('ev-login-form');
    var feedback = document.getElementById('ev-login-feedback');
    if (!form || !feedback) {
        return;
    }

    // TODO integrazione backend:
    // impostare endpoint REST .NET/C# qui o via wp_localize_script in futuro.
    var apiEndpoint = '';

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        var username = form.username ? form.username.value.trim() : '';
        var password = form.password ? form.password.value : '';

        if (!username || !password) {
            feedback.textContent = 'Inserisci username e password.';
            feedback.classList.add('is-error');
            return;
        }

        feedback.classList.remove('is-error', 'is-success');

        if (!apiEndpoint) {
            feedback.textContent = 'Integrazione API non ancora configurata.';
            feedback.classList.add('is-error');
            return;
        }

        fetch(apiEndpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                username: username,
                password: password,
                remember: !!(form.remember && form.remember.checked)
            })
        })
        .then(function (response) {
            if (!response.ok) {
                throw new Error('Credenziali non valide');
            }
            return response.json();
        })
        .then(function () {
            feedback.textContent = 'Accesso effettuato con successo.';
            feedback.classList.add('is-success');
        })
        .catch(function (error) {
            feedback.textContent = error && error.message ? error.message : 'Accesso non riuscito.';
            feedback.classList.add('is-error');
        });
    });
})();