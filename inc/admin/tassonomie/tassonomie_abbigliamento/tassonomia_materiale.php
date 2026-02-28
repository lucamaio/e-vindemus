<?php

/**
 * Definisce la tassonomia taglia per l'abbigliamento
 */

add_action('init', 'evindemus_register_tassonomia_materiale');

function evindemus_register_tassonomia_materiale() {
    $labels = array(
        'name' => _x('Materiali', 'taxonomy general name', 'e-vindemus'),
        'singular_name' => _x('Materiale', 'taxonomy singular name', 'e-vindemus'),
        'search_items' => __('Cerca materiali', 'e-vindemus'),
        'all_items' => __('Tutti i materiali', 'e-vindemus'),
        'parent_item' => __('Materiale genitore', 'e-vindemus'),
        'parent_item_colon' => __('Materiale genitore:', 'e-vindemus'),
        'edit_item' => __('Modifica materiale', 'e-vindemus'),
        'update_item' => __('Aggiorna materiale', 'e-vindemus'),
        'add_new_item' => __('Aggiungi nuovo materiale', 'e-vindemus'),
        'new_item_name' => __('Nome nuovo materiale', 'e-vindemus'),
        'menu_name' => __('Materiali', 'e-vindemus'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        // Non è necessario un rewrite specifico per una tassonomia figlia, erediterà quello del genitore
    );

    register_taxonomy('materiale', array('prodotto'), $args);
}

?>