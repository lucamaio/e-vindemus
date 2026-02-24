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
        'title'        => __('Opzioni homepage', 'E-vindemus'),
        'object_types' => ['options-page'],
        'option_key'   => 'homepage',
        'capability'   => 'manage_options',
        'parent_slug'  => 'themes.php',
        'menu_title'   => __('Opzioni homepage', 'E-vindemus'),
        'tab_title'    => __('Home page', 'E-vindemus'),
    ];

    $home_options = new_cmb2_box($args);

    $home_options->add_field([
        'name'       => __('Titolo sito *', 'E-vindemus'),
        'desc'       => __('Inserisci il titolo mostrato nella homepage.', 'E-vindemus'),
        'id'         => $prefix . 'home_site_title',
        'type'       => 'text',
        'default'    => 'E-vindemus',
        'attributes' => [
            'required'    => 'required',
            'aria-required' => 'true',
        ],
    ]);

    $home_options->add_field([
        'name'    => __('Motto sito', 'E-vindemus'),
        'desc'    => __('Breve descrizione del negozio mostrata nella homepage.', 'E-vindemus'),
        'id'      => $prefix . 'home_site_motto',
        'type'    => 'text',
        'default' => __('Il tuo negozio di vini online', 'E-vindemus'),
    ]);

    $home_options->add_field([
        'name'         => __('Immagini carousel', 'E-vindemus'),
        'desc'         => __('Carica immagini con testo alternativo per migliorare l’accessibilità.', 'E-vindemus'),
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
