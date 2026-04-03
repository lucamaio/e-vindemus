<?php
/**
 * Template Name: Pagina Login
 * Template Post Type: page
 */

get_header();

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (isset($_SESSION['user_id']) || isset($_SESSION['token'])) {
    wp_safe_redirect(home_url());
    exit;
}

$url_API = get_option('homepage', [])['home_api_url'] . '/api/auth/login' ?? '';
$session_init_url = home_url('/inizializza-sessione/');
?>

<main>
    <section class="ev-login-page" aria-label="Pagina accesso account">
        <section class="ev-login-section" aria-label="Modulo login account">
            <div class="ev-login-card">
                <div class="ev-login-card__head">
                    <h1>Accedi al tuo account</h1>
                    <p>Inserisci le credenziali per accedere all area personale.</p>
                </div>

                <form id="ev-login-form" class="ev-login-form" method="post" novalidate data-api-endpoint="<?php echo esc_url($url_API); ?>" data-session-init-url="<?php echo esc_url($session_init_url); ?>">
                    <div class="ev-login-form__field">
                        <label for="ev-login-username">Email</label>
                        <input id="ev-login-username" name="email" type="text" autocomplete="email" required>
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

                <p id="ev-login-feedback" class="ev-login-feedback" aria-live="polite" hidden></p>
                <p class="ev-login-meta ev-login-meta--muted">
                    <a href="<?php echo esc_url(home_url('/reset-password')); ?>">Hai dimenticato la password?</a>
                </p>
                <p class="ev-login-meta">Non hai un account? <a href="<?php echo esc_url(home_url('/registrati')); ?>">Registrati</a></p>
            </div>
        </section>
    </section>
</main>

<script src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/login.js'); ?>"></script>

<?php get_footer(); ?>
