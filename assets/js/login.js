(function () {
    var form = document.getElementById('ev-login-form');
    var feedback = document.getElementById('ev-login-feedback');
    if (!form || !feedback) {
        return;
    }

    var apiEndpoint = form.getAttribute('data-api-endpoint') || '';  // URL API LOGIN
    var sessionInitUrl = form.getAttribute('data-session-init-url') || '';

    function setFeedback(message, type) {
        var hasMessage = !!message;

        feedback.hidden = !hasMessage;
        feedback.textContent = message || '';
        feedback.classList.remove('is-error', 'is-success', 'is-loading');

        if (hasMessage && type) {
            feedback.classList.add(type);
        }
    }

    function submitSessionInit(payload) {
        if (!sessionInitUrl) {
            setFeedback('Pagina inizializzazione sessione non configurata.', 'is-error');
            return;
        }

        var sessionForm = document.createElement('form');
        sessionForm.method = 'post';
        sessionForm.action = sessionInitUrl;
        sessionForm.style.display = 'none';

        Object.keys(payload).forEach(function (key) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = payload[key];
            sessionForm.appendChild(input);
        });

        document.body.appendChild(sessionForm);
        sessionForm.submit();
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        var email = form.email ? form.email.value.trim() : '';
        var password = form.password ? form.password.value : '';

        // DEBUG
        // console.log(email);
        // console.log(password);

        if (!email || !password) {
            setFeedback('Inserisci email e password.', 'is-error');
            return;
        }

        if (!apiEndpoint) {
            setFeedback('Endpoint API login non configurato.', 'is-error');
            return;
        }

        setFeedback('Verifica credenziali in corso...', 'is-loading');

        fetch(apiEndpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email: email,
                password: password
            })
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Credenziali non valide');
                }
                return response.json();
            })
            .then(function (data) {
                var userId = data && data.user && data.user.id ? data.user.id : '';
                var token = data && data.token ? data.token : '';

                if (!userId || !token) {
                    throw new Error('Risposta login non valida');
                }

                setFeedback('Accesso effettuato con successo. Ti stiamo reindirizzando...', 'is-success');
                setTimeout(function () {
                    submitSessionInit({
                        user_id: userId,
                        token: token
                    });
                }, 200);
            })
            .catch(function (error) {
                setFeedback(error && error.message ? error.message : 'Accesso non riuscito.', 'is-error');
            });
    });
})();
