<?php

/* 
    Definisco la tipologia "Notizia". Questa tipologia sarà utilizzata per gestire i prodotti del nostro e-commerce.
*/

add_action('init', 'dci_register_tipologia_notizia');

function dci_register_tipologia_notizia() {
    $labels = array(
        'name'               => _x('Notizie', 'post type general name', 'e-vindemus'),
        'singular_name'      => _x('Notizia', 'post type singular name', 'e-vindemus'),
        'menu_name'         => _x('Notizie', 'admin menu', 'e-vindemus'),
        'name_admin_bar'    => _x('Notizia', 'add new on admin bar', 'e-vindemus'),
        'add_new'           => _x('Aggiungi Nuova', 'notizia', 'e-vindemus'),
        'add_new_item'      => __('Aggiungi Nuova Notizia', 'e-vindemus'),
        'new_item'          => __('Nuova Notizia', 'e-vindemus'),
        'edit_item'         => __('Modifica Notizia', 'e-vindemus'),
        'view_item'         => __('Visualizza Notizia', 'e-vindemus'),
        'all_items'         => __('Tutte le Notizie', 'e-vindemus'),
        'search_items'      => __('Cerca Notizie', 'e-vindemus'),
        'parent_item_colon' => __('Notizie Genitore:', 'e-vindemus'),
        'not_found'         => __('Nessuna notizia trovata.', 'e-vindemus'),
        'not_found_in_trash'=> __('Nessuna notizia trovata nel cestino.', 'e-vindemus')
    );

    $args = array(
        'label'             => __('Notizia', 'e-vindemus'),
        'labels'             => $labels,
        'supports'          => array('title', 'editor', 'author'),
        'hierarchical'      => false,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'notizia'),
        'capability_type'    => array('notizia', 'notizie'),
        'capabilities' => array(
            'edit_post' => 'edit_notizia',
            'read_post' => 'read_notizia',
            'delete_post' => 'delete_notizia',
            'edit_posts' => 'edit_notizie',
            'edit_others_posts' => 'edit_others_notizie',
            'publish_posts' => 'publish_notizie',
            'read_private_posts' => 'read_private_notizie',
            'delete_posts' => 'delete_notizie',
            'delete_private_posts' => 'delete_private_notizie',
            'delete_published_posts' => 'delete_published_notizie',
            'delete_others_posts' => 'delete_others_notizie',
            'edit_private_posts' => 'edit_private_notizie',
            'edit_published_posts' => 'edit_published_notizie',
            'create_posts' => 'create_notizie'
        ),
        'has_archive'       => false,
        'menu_position'     => 5,
        'map_meta_cap'       => true, 
        'menu_icon'     => 'dashicons-products', // Icona del menu (puoi scegliere un'icona diversa da https://developer.wordpress.org/resource/dashicons/#cart),
        'description'     => __('Tipologia per gestire le notizie del nostro e-commerce.', 'e-vindemus')
    );

    register_post_type('notizia', $args);

    remove_post_type_support('notizia', 'editor'); // Rimuovo l'editor classico per le notizie, in quanto utilizzeremo i campi custom per gestire le informazioni della notizia.
}

/*
    Aggiunfo i campi relativi alla tipologia "Notizia". In questo caso, aggiungo un campo per il prezzo della notizia.
*/

add_action('cmb2_admin_init', 'dci_add_notizia_metaboxes');

function dci_add_notizia_metaboxes() {
    if (!function_exists('new_cmb2_box')) {
        return;
    }

    $prefix = '_dci_notizia_';

    /**
     *  Campi sezione Principale
     */

    $cmb_apertura = new_cmb2_box([
        'id'            => $prefix . 'apertura',
        'title'         => __('Apertura', 'e-vindemus'),
        'object_types'  => ['notizia'],
        'context'       => 'normal',
        'priority'      => 'high',
        'show_names'    => true,
    ]);

    // immagine in evidenza
    $cmb_apertura->add_field([
        'name' => __('Immagine in evidenza', 'e-vindemus'),
        'id'   => $prefix . 'immagine_evidenza',
        'type' => 'file',
        'options' => [
            'url' => false, // Disabilita l'inserimento manuale dell'URL
        ],
        'attributes' => [
            'required'      => 'required',
            'aria-required' => 'true',
        ],
    ]);

    // Descrizione breve (max 300 caratteri)
    $cmb_apertura->add_field([
        'name' => __('Descrizione breve *', 'e-vindemus'),
        'id'   => $prefix . 'descrizione_breve',
        'desc' => __('Breve descrizione della notizia, visualizzata nelle anteprime. Massimo 300 caratteri.', 'e-vindemus'),
        'type' => 'wysiwyg',
        'options' => [
            'textarea_rows' => 5,
            'media_buttons' => false,
        ],
        'attributes' => [
            'required'      => 'required',
            'aria-required' => 'true',
            'maxlength'      => 300,
        ],
    ]);


    // Sezione Contenuto notizia

    $cmb_contenuto = new_cmb2_box([
        'id'            => $prefix . 'contenuto',
        'title'         => __('Contenuto Notizia', 'e-vindemus'),
        'object_types'  => ['notizia'],
        'context'       => 'normal',
        'priority'      => 'high',
        'show_names'    => true,
    ]);

    $cmb_contenuto->add_field([
        'name' => __('Testo completo della notizia', 'e-vindemus'),
        'id'   => $prefix . 'testo_completo',
        'type' => 'wysiwyg',
        'options' => [
            'textarea_rows' => 10,
            'media_buttons' => true,
        ],
        'attributes' => [
            'required'      => 'required',
            'aria-required' => 'true',
        ],
    ]);

    // Allegati 

    $cmb_contenuto-> add_field([
        'name' => __('Allegati', 'e-vindemus'),
        'id'   => $prefix . 'allegati',
        'type' => 'file_list',
        'options' => [
            'url' => false, // Disabilita l'inserimento manuale dell'URL
        ],
    ]);

    // Link esterni

    $cmb_contenuto-> add_field([
        'name' => __('Link esterni', 'e-vindemus'),
        'id'   => $prefix . 'link_esterni',
        'type' => 'group',
        'options' => [
            'group_title'   => __('Link {#}', 'e-vindemus'),
            'add_button'   => __('Aggiungi Link', 'e-vindemus'),
            'remove_button' => __('Rimuovi Link', 'e-vindemus'),
            'sortable'      => true,
        ],
    ]);

    $cmb_contenuto->add_group_field($prefix . 'link_esterni', [
        'name' => __('URL', 'e-vindemus'),
        'id'   => 'url',
        'type' => 'text_url',
        'attributes' => [
            'required'      => 'required',
            'aria-required' => 'true',
        ],
    ]);

    $cmb_contenuto->add_group_field($prefix . 'link_esterni', [
        'name' => __('Testo del link', 'e-vindemus'),
        'id'   => 'testo',
        'type' => 'text',
        'attributes' => [
            'required'      => 'required',
            'aria-required' => 'true',
        ],
    ]);

    $cmb_contenuto->add_group_field($prefix . 'link_esterni', [
        'name' => __('Apri in una nuova finestra', 'e-vindemus'),
        'id'   => 'nuova_finestra',
        'type' => 'checkbox',
    ]);

    // Sezione Tipologia notizia

    $cmb_tipologia = new_cmb2_box([
        'id'            => $prefix . 'tipologia',
        'title'         => __('Tipologia Notizia', 'e-vindemus'),
        'object_types'  => ['notizia'],
        'context'       => 'side',
        'priority'      => 'high',
        'show_names'    => true,
    ]);

    $cmb_tipologia->add_field([
        'name' => __('Tipologia della notizia', 'e-vindemus'),
        'id'   => $prefix . 'tipologia_notizia',
        'type' => 'taxonomy_radio_hierarchical',
        'taxonomy' => 'tipologia_notizia', // Assicurati di avere una tassonomia chiamata "tipologia_notizia"
        'attributes' => [
            'required'      => 'required',
            'aria-required' => 'true',
        ],
        'remove_default' => 'true', // Rimuove il metabox di default per questa tassonomia
        'show_option_none' => false // Opzione "Nessuna" personalizzata
    ]);

}