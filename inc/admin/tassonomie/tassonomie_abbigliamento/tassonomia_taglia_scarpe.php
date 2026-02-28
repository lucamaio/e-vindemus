<?php

/**
 * Definisce la tassonomia taglia di scarpe per l'abbigliamento
 */

add_action('init', 'evindemus_register_tassonomia_taglia_scarpe');

function evindemus_register_tassonomia_taglia_scarpe() {
    $labels = array(
        'name' => _x('Taglie Scarpe', 'taxonomy general name', 'e-vindemus'),
        'singular_name' => _x('Taglia Scarpe', 'taxonomy singular name', 'e-vindemus'),
        'search_items' => __('Cerca taglie scarpe', 'e-vindemus'),
        'all_items' => __('Tutte le taglie scarpe', 'e-vindemus'),
        'parent_item' => __('Taglia scarpe genitore', 'e-vindemus'),
        'parent_item_colon' => __('Taglia scarpe genitore:', 'e-vindemus'),
        'edit_item' => __('Modifica taglia scarpe', 'e-vindemus'),
        'update_item' => __('Aggiorna taglia scarpe', 'e-vindemus'),
        'add_new_item' => __('Aggiungi nuova taglia scarpe', 'e-vindemus'),
        'new_item_name' => __('Nome nuova taglia scarpe', 'e-vindemus'),
        'menu_name' => __('Taglie Scarpe', 'e-vindemus'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        // Non è necessario un rewrite specifico per una tassonomia figlia, erediterà quello del genitore
    );

    register_taxonomy('taglia_scarpe', array('prodotto'), $args);
}

?>  