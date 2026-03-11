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

<script src="'<?php echo get_template_directory(); ?>'/assets/js/login.js"></script>

<?php get_footer(); ?>
