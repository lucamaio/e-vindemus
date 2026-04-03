<?php
/**
 * Template Name: Pagina Registrati
 * Template Post Type: page
 */

get_header();

$url_API = get_option('homepage', [])['home_login_api_url'] . '/api/users' ?? '';
?>

<main>
    <section class="ev-login-page" aria-label="Pagina registrazione account">
        <section class="ev-login-section" aria-label="Modulo registrazione account">
            <div class="ev-login-card">
                <div class="ev-login-card__head">
                    <h1>Crea il tuo account</h1>
                    <p>Compila i campi per registrarti e iniziare a usare l area personale.</p>
                </div>

                <form id="ev-register-form" class="ev-login-form" method="post" novalidate data-api-endpoint="<?php echo esc_url($url_API); ?>">
                    <div class="ev-login-form__field">
                        <label for="ev-register-first-name">Nome</label>
                        <input id="ev-register-first-name" name="firstName" type="text" autocomplete="given-name" required>
                    </div>

                    <div class="ev-login-form__field">
                        <label for="ev-register-last-name">Cognome</label>
                        <input id="ev-register-last-name" name="lastName" type="text" autocomplete="family-name" required>
                    </div>

                    <div class="ev-login-form__field">
                        <label for="ev-register-email">Email</label>
                        <input id="ev-register-email" name="email" type="email" autocomplete="email" required>
                    </div>

                    <div class="ev-login-form__field">
                        <label for="ev-register-password">Password</label>
                        <input id="ev-register-password" name="password" type="password" autocomplete="new-password" required>
                    </div>

                    <button type="submit" class="ev-btn ev-btn--primary ev-login-form__submit">Registrati</button>
                </form>

                <p id="ev-register-feedback" class="ev-login-feedback" aria-live="polite" hidden></p>
                <p class="ev-login-meta">Hai gia un account? <a href="<?php echo esc_url(home_url('/login')); ?>">Accedi</a></p>
            </div>
        </section>
    </section>
</main>

<script src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/register.js'); ?>"></script>

<?php get_footer(); ?>
