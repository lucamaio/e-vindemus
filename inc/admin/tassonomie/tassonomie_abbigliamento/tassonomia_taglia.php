<?php

/**
 * Definisce la tassonomia taglia per l'abbigliamento
 */

add_action('init', 'evindemus_register_tassonomia_taglia');

function evindemus_register_tassonomia_taglia() {
    $labels = array(
        'name' => _x('Taglie', 'taxonomy general name', 'e-vindemus'),
        'singular_name' => _x('Taglia', 'taxonomy singular name', 'e-vindemus'),
        'search_items' => __('Cerca taglie', 'e-vindemus'),
        'all_items' => __('Tutte le taglie', 'e-vindemus'),
        'parent_item' => __('Taglia genitore', 'e-vindemus'),
        'parent_item_colon' => __('Taglia genitore:', 'e-vindemus'),
        'edit_item' => __('Modifica taglia', 'e-vindemus'),
        'update_item' => __('Aggiorna taglia', 'e-vindemus'),
        'add_new_item' => __('Aggiungi nuova taglia', 'e-vindemus'),
        'new_item_name' => __('Nome nuova taglia', 'e-vindemus'),
        'menu_name' => __('Taglie', 'e-vindemus'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
    );

    register_taxonomy('taglia', array('prodotto'), $args);
}

?>