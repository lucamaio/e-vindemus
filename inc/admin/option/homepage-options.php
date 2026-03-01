<?php

/**
 * Registra la pagina opzioni homepage (CMB2).
 */
function dci_register_homepage_options() {
    if (!function_exists('new_cmb2_box')) {
        return;
    }

    $prefix = '';

    $args = [
        'id'            => $prefix . 'dci_homepage_options',
        'title'         => __('Opzioni homepage', 'e-vindemus'),
        'object_types'  => ['options-page'],
        'option_key'    => 'homepage',
        'capability'    => 'manage_options',
        'menu_title'    => __('Configurazione', 'e-vindemus'),
        'menu_position' => 5,
        'tab_title'     => __('Home page', 'e-vindemus'),
    ];

    $home_options = new_cmb2_box($args);

    $home_options->add_field([
        'id'   => $prefix . 'opzioni_configurazione_homepage',
        'name' => __('Impostazioni generali homepage', 'e-vindemus'),
        'desc' => __('Definisci i contenuti principali della homepage: identita del sito, testo istituzionale e media del carousel.', 'e-vindemus'),
        'type' => 'title',
        'attributes' => [
            'class' => 'dci-homepage-section-title bg-blue',
        ],
    ]);

    $home_options->add_field([
        'name'       => __('Titolo sito *', 'e-vindemus'),
        'desc'       => __('Titolo principale visualizzato nella homepage e nei punti strategici del tema. Usa una dicitura chiara e riconoscibile dal brand.', 'e-vindemus'),
        'id'         => $prefix . 'home_site_title',
        'type'       => 'text',
        'default'    => 'e-vindemus',
        'attributes' => [
            'required'      => 'required',
            'aria-required' => 'true',
        ],
    ]);

    $home_options->add_field([
        'name'    => __('Motto sito', 'e-vindemus'),
        'desc'    => __('Messaggio breve a supporto del titolo: comunica proposta di valore, target o tone of voice del negozio.', 'e-vindemus'),
        'id'      => $prefix . 'home_site_motto',
        'type'    => 'text',
        'default' => __('Il tuo negozio di vini online', 'e-vindemus'),
    ]);

    $home_options->add_field([
        'name'         => __('Immagini carousel', 'e-vindemus'),
        'desc'         => __('Carica le immagini del carousel homepage. Mantieni formato e stile coerenti e aggiungi testi alternativi per accessibilita e SEO.', 'e-vindemus'),
        'id'           => $prefix . 'home_carousel_images',
        'type'         => 'file_list',
        'options'      => [
            'url' => false,
        ],
        'preview_size' => [100, 100],
        'query_args'   => ['type' => 'image'],
    ]);

    $home_options->add_field([
        'id'   => $prefix . 'home_alert_section',
        'name' => __('Configurazione barra alert', 'e-vindemus'),
        'desc' => __('Imposta la comunicazione rapida in testata (promozioni, spedizioni, avvisi). Puoi controllarne contenuto, stile e periodo di pubblicazione.', 'e-vindemus'),
        'type' => 'title',
    ]);

    $home_options->add_field([
        'id'   => $prefix . 'home_alert_message',
        'name' => __('Testo messaggio', 'e-vindemus'),
        'desc' => __('Contenuto visualizzato nella barra alert in alto. Usa un testo breve, diretto e orientato all azione.', 'e-vindemus'),
        'type' => 'text',
    ]);

    $home_options->add_field([
        'id'      => $prefix . 'home_alert_color',
        'name'    => __('Colore barra alert', 'e-vindemus'),
        'desc'    => __('Seleziona il colore dell alert in base alla priorita del messaggio (informativo, promozionale o urgente).', 'e-vindemus'),
        'type'    => 'select',
        'options' => [
            'yellow' => __('Giallo', 'e-vindemus'),
            'blue'   => __('Blu', 'e-vindemus'),
            'red'    => __('Rosso', 'e-vindemus'),
            'green'  => __('Verde', 'e-vindemus'),
        ],
    ]);

    $home_options->add_field([
        'id'   => $prefix . 'home_alert_show',
        'name' => __('Mostra barra alert', 'e-vindemus'),
        'desc' => __('Attiva o disattiva rapidamente la visualizzazione della barra alert in homepage.', 'e-vindemus'),
        'type' => 'checkbox',
    ]);

    $home_options->add_field([
        'id'   => $prefix . 'home_alert_start_date',
        'name' => __('Data inizio alert', 'e-vindemus'),
        'desc' => __('Data/ora di inizio pubblicazione dell alert. Se vuota, l alert puo partire immediatamente.', 'e-vindemus'),
        'type' => 'text_date_timestamp',
    ]);

    $home_options->add_field([
        'id'   => $prefix . 'home_alert_end_date',
        'name' => __('Data fine alert', 'e-vindemus'),
        'desc' => __('Data/ora di disattivazione dell alert. Se vuota, l alert resta visibile finche non viene disabilitato manualmente.', 'e-vindemus'),
        'type' => 'text_date_timestamp',
    ]);
}
add_action('cmb2_admin_init', 'dci_register_homepage_options');

/**
 * Carica stile e script admin dedicati alla pagina Configurazione (homepage options).
 */
function dci_enqueue_homepage_options_admin_assets() {
    if (!is_admin()) {
        return;
    }

    $current_page = isset($_GET['page']) ? sanitize_key((string) $_GET['page']) : '';
    if ($current_page !== 'homepage') {
        return;
    }

    wp_enqueue_style(
        'ev-homepage-options-admin',
        get_template_directory_uri() . '/inc/admin/option/homepage-options-admin.css',
        [],
        '1.1.0'
    );

    wp_enqueue_script(
        'ev-homepage-options-admin',
        get_template_directory_uri() . '/inc/admin/option/homepage-options-admin.js',
        [],
        '1.1.0',
        true
    );
}
add_action('admin_enqueue_scripts', 'dci_enqueue_homepage_options_admin_assets');
