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
        'menu_position' => 5,
        'tab_title'    => __('Home page', 'e-vindemus'),
    ];

    $home_options = new_cmb2_box($args);

    
    $home_options->add_field( array(
        'id'   => $prefix . 'opzioni_configurazione_homepage',
        'name' => __( 'Impostazioni info homepage', 'e-vindemus' ),
        'desc' => __( 'Configura le informazioni principali della homepage.', 'e-vindemus' ),
        'type' => 'title',
        'attributes' => array(
            'class' => 'dci-homepage-section-title bg-blue',
            'background-color' => '#0073aa',
            'color' => '#fff',
        ),
    ) );

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

    // Sezione 2: Configurazione barra alert
    $home_options->add_field( array(
        'id'   => $prefix . 'home_alert_section',
        'name' => __( 'Configurazione barra alert', 'e-vindemus' ),
        'desc' => __( 'Configura il messaggio in alto della homepage e il colore della barra alert.', 'e-vindemus' ),
        'type' => 'title',
    ) );

    $home_options->add_field( array(
        'id'   => $prefix . 'home_alert_message',
        'name' => __( 'Testo messaggio', 'e-vindemus' ),
        'desc' => __( 'Il testo viene mostrato nella barra superiore della homepage.', 'e-vindemus' ),
        'type' => 'text'
    ));

    $home_options->add_field( array(
        'id'   => $prefix . 'home_alert_color',
        'name' => __( 'Colore barra alert', 'e-vindemus' ),
        'desc' => __( 'Scegli il colore della barra alert.', 'e-vindemus' ),
        'type' => 'select',
        'options' => [
            'yellow' => __('Giallo', 'e-vindemus'),
            'blue'   => __('Blu', 'e-vindemus'),
            'red'    => __('Rosso', 'e-vindemus'),
            'green'  => __('Verde', 'e-vindemus'),
        ],
    ) );

    $home_options->add_field( array(
        'id'   => $prefix . 'home_alert_show',
        'name' => __( 'Mostra barra alert', 'e-vindemus' ),
        'desc' => __( 'Abilita o disabilita la visualizzazione della barra alert nella homepage.', 'e-vindemus' ),
        'type' => 'checkbox',
    ) );

    // Data inizio e fine alert
    $home_options->add_field( array(
        'id'   => $prefix . 'home_alert_start_date',
        'name' => __( 'Data inizio alert', 'e-vindemus' ),
        'desc' => __( 'Scegli la data di inizio per mostrare la barra alert.', 'e-vindemus' ),
        'type' => 'text_date_timestamp',
    ) );

    $home_options->add_field( array(
        'id'   => $prefix . 'home_alert_end_date',
        'name' => __( 'Data fine alert', 'e-vindemus' ),
        'desc' => __( 'Scegli la data di fine per mostrare la barra alert.', 'e-vindemus' ),
        'type' => 'text_date_timestamp',
    ) );

    // Sezione 3: Selezione dei prodotti in evidenza

    $home_options->add_field( array(
        'id'   => $prefix . 'home_featured_products_section',
        'name' => __( 'Configurazione prodotti in evidenza', 'e-vindemus' ),
        'desc' => __( 'Seleziona i prodotti da mostrare in evidenza nella homepage.', 'e-vindemus' ),
        'type' => 'title',
    ) );

    $home_options->add_field( array(
            'name' => __('<h5>Selezione prodotti in evidenza</h5>', 'design_comuni_italia'),
            'desc' => __('Seleziona i prodotti da mostrare in evidenza nella homepage ', 'design_comuni_italia'),
            'id' => $prefix . 'prodotti_evidenziati',
            'type'    => 'custom_attached_posts',
            'column'  => true,
            'options' => array(
                'show_thumbnails' => false,
                'filter_boxes'    => true,
                'query_args'      => array(
                    'posts_per_page' => -1,
                    'post_type'      => array('prodotto'),
                ),
            ),
            'attributes' => array(
                'data-max-items' => 6,
            ),
        ));
    


}
add_action('cmb2_admin_init', 'dci_register_homepage_options');
