<?php

/**
 *  Definisce la tassonomia "Posizioni evidenziata" per i prodotti.
 *  Questa tassonomia consente di categorizzare i prodotti in base alla posizione in cui devono essere evidenziati (es. homepage, offerte, ecc.).
 *  Viene registrata come tassonomia non gerarchica (simile ai tag) associata al post type "product".
 *  
 */

add_action('init', 'evindemus_register_tassonomia_posizioni_evidenziata');

function evindemus_register_tassonomia_posizioni_evidenziata() {
    $labels = array(
        'name' => _x('Posizioni evidenziata', 'taxonomy general name', 'e-vindemus'),
        'singular_name' => _x('Posizione evidenziata', 'taxonomy singular name', 'e-vindemus'),
        'search_items' => __('Cerca posizioni evidenziata', 'e-vindemus'),
        'all_items' => __('Tutte le posizioni evidenziata', 'e-vindemus'),
        'edit_item' => __('Modifica posizione evidenziata', 'e-vindemus'),
        'update_item' => __('Aggiorna posizione evidenziata', 'e-vindemus'),
        'add_new_item' => __('Aggiungi nuova posizione evidenziata', 'e-vindemus'),
        'new_item_name' => __('Nome nuova posizione evidenziata', 'e-vindemus'),
        'menu_name' => __('Posizioni evidenziata', 'e-vindemus'),
    );

    $args = array(
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'posizione-evidenziata'),
    );

    register_taxonomy('posizione_evidenziata', array('prodotto'), $args);

}

?>