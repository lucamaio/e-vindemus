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
        'id'           => $prefix . 'dci_homepage_options',
        'title'        => __('Opzioni homepage', 'e-vindemus'),
        'object_types' => ['options-page'],
        'option_key'   => 'homepage',
        'capability'   => 'manage_options',
        // 'parent_slug'  => 'themes.php',
        'menu_title'   => __('Configurazione homepage', 'e-vindemus'),
        'menu_position' => 6,
        'tab_title'    => __('Home page', 'e-vindemus'),
    ];

    $home_options = new_cmb2_box($args);

    $home_options->add_field([
        'name'       => __('Titolo sito *', 'e-vindemus'),
        'desc'       => __('Inserisci il titolo mostrato nella homepage.', 'e-vindemus'),
        'id'         => $prefix . 'home_site_title',
        'type'       => 'text',
        'default'    => 'e-vindemus',
        'attributes' => [
            'required'    => 'required',
            'aria-required' => 'true',
        ],
    ]);

    $home_options->add_field([
        'name'    => __('Motto sito', 'e-vindemus'),
        'desc'    => __('Breve descrizione del negozio mostrata nella homepage.', 'e-vindemus'),
        'id'      => $prefix . 'home_site_motto',
        'type'    => 'text',
        'default' => __('Il tuo negozio di vini online', 'e-vindemus'),
    ]);

    $home_options->add_field([
        'name'         => __('Immagini carousel', 'e-vindemus'),
        'desc'         => __('Carica immagini con testo alternativo per migliorare l’accessibilità.', 'e-vindemus'),
        'id'           => $prefix . 'home_carousel_images',
        'type'         => 'file_list',
        'options'      => [
            'url' => false,
        ],
        'preview_size' => [100, 100],
        'query_args'   => ['type' => 'image'],
    ]);
}
add_action('cmb2_admin_init', 'dci_register_homepage_options');
