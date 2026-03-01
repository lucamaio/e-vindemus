<?php 

/**
 * Definisce la tassonomia marchio per l'abbigliamento
 * associata al post type "prodotto"
 * Adesempio Adidas, Nike, Puma, ecc.
 */

add_action('init', 'evindemus_register_tassonomia_marchio');

function evindemus_register_tassonomia_marchio() {
    $labels = array(
        'name' => _x('Marchi', 'taxonomy general name', 'e-vindemus'),
        'singular_name' => _x('Marchio', 'taxonomy singular name', 'e-vindemus'),
        'search_items' => __('Cerca marchi', 'e-vindemus'),
        'all_items' => __('Tutti i marchi', 'e-vindemus'),
        'parent_item' => __('Marchio genitore', 'e-vindemus'),
        'parent_item_colon' => __('Marchio genitore:', 'e-vindemus'),
        'edit_item' => __('Modifica marchio', 'e-vindemus'),
        'update_item' => __('Aggiorna marchio', 'e-vindemus'),
        'add_new_item' => __('Aggiungi nuovo marchio', 'e-vindemus'),
        'new_item_name' => __('Nome nuovo marchio', 'e-vindemus'),
        'menu_name' => __('Marchi', 'e-vindemus'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
    );

    register_taxonomy('marchio', array('prodotto'), $args);
}

?>