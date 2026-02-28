<?php

/**
 * Definisce i sessi per l'abbigliamento
 */

add_action('init', 'evindemus_register_tassonomia_sesso');

function evindemus_register_tassonomia_sesso() {
    $labels = array(
        'name' => _x('Sessi', 'taxonomy general name', 'e-vindemus'),
        'singular_name' => _x('Sesso', 'taxonomy singular name', 'e-vindemus'),
        'search_items' => __('Cerca sessi', 'e-vindemus'),
        'all_items' => __('Tutti i sessi', 'e-vindemus'),
        'parent_item' => __('Sesso genitore', 'e-vindemus'),
        'parent_item_colon' => __('Sesso genitore:', 'e-vindemus'),
        'edit_item' => __('Modifica sesso', 'e-vindemus'),
        'update_item' => __('Aggiorna sesso', 'e-vindemus'),
        'add_new_item' => __('Aggiungi nuovo sesso', 'e-vindemus'),
        'new_item_name' => __('Nome nuovo sesso', 'e-vindemus'),
        'menu_name' => __('Sessi', 'e-vindemus'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
    );

    register_taxonomy('sesso', array('prodotto'), $args);
}
?>