<?php
/**
 * Template Name: Pagina Login
 * Template Post Type: page
 */

get_header();
?>

<main>
    <section class="ev-login-page" aria-label="Pagina accesso account">
        <section class="ev-login-section" aria-label="Modulo login account">
            <div class="ev-login-card">
                <div class="ev-login-card__head">
                    <h1>Accedi al tuo account</h1>
                    <p>Inserisci le credenziali per accedere all area personale.</p>
                </div>

                <form id="ev-login-form" class="ev-login-form" method="post" novalidate>
                    <div class="ev-login-form__field">
                        <label for="ev-login-username">Email o username</label>
                        <input id="ev-login-username" name="username" type="text" autocomplete="username" required>
                    </div>

                    <div class="ev-login-form__field">
                        <label for="ev-login-password">Password</label>
                        <input id="ev-login-password" name="password" type="password" autocomplete="current-password" required>
                    </div>

                    <label class="ev-login-form__check">
                        <input type="checkbox" name="remember" value="1">
                        <span>Ricordami su questo dispositivo</span>
                    </label>

                    <button type="submit" class="ev-btn ev-btn--primary ev-login-form__submit">Accedi</button>
                </form>

                <p id="ev-login-feedback" class="ev-login-feedback" aria-live="polite"></p>
            </div>
        </section>
    </section>
</main>

<script>
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
</script>

<?php get_footer(); ?>
