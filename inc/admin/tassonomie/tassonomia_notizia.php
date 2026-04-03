<?php

/**
 * Definisce la tassonomia notizia del prodotto
 * 
 */

add_action('init', 'evindemus_register_tassonomia_tipologia_notizia', 11);

function evindemus_register_tassonomia_tipologia_notizia() {
    $labels = array(
        'name' => _x('Tipologie Notizie', 'taxonomy general name', 'e-vindemus'),
        'singular_name' => _x('Tipologia Notizia', 'taxonomy singular name', 'e-vindemus'),
        'search_items' => __('Cerca Tipologia notizie', 'e-vindemus'),
        'all_items' => __('Tutte le Tipologie Notizie', 'e-vindemus'),
        'parent_item' => __('Tipologia Notizia genitore', 'e-vindemus'),
        'parent_item_colon' => __('Tipologia Notizia genitore:', 'e-vindemus'),
        'edit_item' => __('Modifica Tipologia notizia', 'e-vindemus'),
        'update_item' => __('Aggiorna Tipologia notizia', 'e-vindemus'),
        'add_new_item' => __('Aggiungi nuova tipologia notizia', 'e-vindemus'),
        'new_item_name' => __('Nome nuova tipologia notizia', 'e-vindemus'),
        'menu_name' => __('Tipologie Notizie', 'e-vindemus'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'tipologia-notizia'),
    );

    register_taxonomy('tipologia_notizia', array('notizia'), $args);
    register_taxonomy_for_object_type('tipologia_notizia', 'notizia');

}
