<?php

/**
 * Definisco la tassonomia colore per l'abbigliamento
 */

add_action('init', 'evindemus_register_tassonomia_colore');

function evindemus_register_tassonomia_colore() {
    $labels = array(
        'name' => _x('Colori', 'taxonomy general name', 'e-vindemus'),
        'singular_name' => _x('Colore', 'taxonomy singular name', 'e-vindemus'),
        'search_items' => __('Cerca colori', 'e-vindemus'),
        'all_items' => __('Tutti i colori', 'e-vindemus'),
        'parent_item' => __('Colore genitore', 'e-vindemus'),
        'parent_item_colon' => __('Colore genitore:', 'e-vindemus'),
        'edit_item' => __('Modifica colore', 'e-vindemus'),
        'update_item' => __('Aggiorna colore', 'e-vindemus'),
        'add_new_item' => __('Aggiungi nuovo colore', 'e-vindemus'),
        'new_item_name' => __('Nome nuovo colore', 'e-vindemus'),
        'menu_name' => __('Colori', 'e-vindemus'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
    );

    register_taxonomy('colore', array('prodotto'), $args);
}