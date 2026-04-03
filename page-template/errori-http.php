<?php
/**
 * Template Name: Pagina Errori HTTP
 * Template Post Type: page
 */

get_header();

$error_code = get_query_var('ev_http_error_code');
$error_code = is_numeric($error_code) ? (int) $error_code : 500;

$allowed_codes = [405, 500, 503];
if (!in_array($error_code, $allowed_codes, true)) {
    $error_code = 500;
}

$messages = [
    405 => [
        'title' => '405 - Metodo non consentito',
        'description' => 'La richiesta è stata ricevuta, ma il metodo utilizzato non è consentito per questa risorsa.',
    ],
    500 => [
        'title' => '500 - Errore interno del server',
        'description' => 'Si è verificato un errore interno. Riprova tra qualche minuto.',
    ],
    503 => [
        'title' => '503 - Servizio non disponibile',
        'description' => 'Il servizio è temporaneamente non disponibile. Ti invitiamo a riprovare più tardi.',
    ],
];

$current_message = $messages[$error_code];
?>

<main>
    <section class="container py-5 text-center" aria-label="Pagina errore server">
        <h1 class="mb-3"><?php echo esc_html($current_message['title']); ?></h1>
        <p class="mb-4"><?php echo esc_html($current_message['description']); ?></p>
        <a class="btn btn-primary" href="<?php echo esc_url(home_url('/')); ?>">Torna alla home</a>
    </section>
</main>

<?php get_footer(); ?>
