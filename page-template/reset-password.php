<?php
/**
 * Template Name: Pagina Reset Password
 * Template Post Type: page
 */

get_header();

$api_base_url = trailingslashit(get_option('homepage', [])['home_api_url'] ?? '');
$forgot_password_api_url = $api_base_url !== '' ? $api_base_url . 'api/auth/forgot-password' : '';
$reset_password_api_url = $api_base_url !== '' ? $api_base_url . 'api/auth/reset-password' : '';
$reset_token = isset($_GET['token']) ? sanitize_text_field(wp_unslash((string) $_GET['token'])) : '';
?>

<main>
    <section class="ev-login-page" aria-label="Pagina recupero password account">
        <section class="ev-login-section" aria-label="Modulo recupero password account">
            <div class="ev-login-card">
                <div class="ev-login-card__head">
                    <h1><?php echo $reset_token !== '' ? 'Imposta una nuova password' : 'Recupera la password'; ?></h1>
                    <p><?php echo $reset_token !== '' ? 'Inserisci una nuova password per completare il recupero dell account.' : 'Inserisci la tua email e ti guideremo nel ripristino dell accesso.'; ?></p>
                </div>

                <form
                    id="ev-reset-password-form"
                    class="ev-login-form"
                    method="post"
                    novalidate
                    data-forgot-api-endpoint="<?php echo esc_url($forgot_password_api_url); ?>"
                    data-reset-api-endpoint="<?php echo esc_url($reset_password_api_url); ?>"
                    data-login-url="<?php echo esc_url(home_url('/login')); ?>"
                    data-reset-token="<?php echo esc_attr($reset_token); ?>"
                >
                    <div class="ev-login-form__field ev-reset-password-form__email">
                        <label for="ev-reset-password-email">Email</label>
                        <input id="ev-reset-password-email" name="email" type="email" autocomplete="email" required>
                    </div>

                    <div class="ev-login-form__field ev-reset-password-form__token">
                        <label for="ev-reset-password-token">Token di reset</label>
                        <input id="ev-reset-password-token" name="token" type="text" autocomplete="off">
                    </div>

                    <div class="ev-login-form__field ev-reset-password-form__new-password">
                        <label for="ev-reset-password-new-password">Nuova password</label>
                        <input id="ev-reset-password-new-password" name="newPassword" type="password" autocomplete="new-password" required>
                    </div>

                    <div class="ev-login-form__field ev-reset-password-form__confirm-password">
                        <label for="ev-reset-password-confirm-password">Conferma nuova password</label>
                        <input id="ev-reset-password-confirm-password" name="confirmPassword" type="password" autocomplete="new-password" required>
                    </div>

                    <button type="submit" class="ev-btn ev-btn--primary ev-login-form__submit" data-default-label="Invia link di recupero">
                        <?php echo $reset_token !== '' ? 'Aggiorna password' : 'Invia link di recupero'; ?>
                    </button>
                </form>

                <p id="ev-reset-password-feedback" class="ev-login-feedback" aria-live="polite" hidden></p>
                <p id="ev-reset-password-dev-token" class="ev-login-feedback ev-login-feedback--muted" hidden></p>
                <p class="ev-login-meta"><a href="<?php echo esc_url(home_url('/login')); ?>">Torna al login</a></p>
            </div>
        </section>
    </section>
</main>

<script src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/reset-password.js'); ?>"></script>

<?php get_footer(); ?>
