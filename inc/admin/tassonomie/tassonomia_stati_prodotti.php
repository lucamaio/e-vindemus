<?php

/**
 * Definisce la tassonomia stato del prodotto
 * 
 */

add_action('init', 'evindemus_register_tassonomia_stati_prdotti');

function evindemus_register_tassonomia_stati_prdotti() {
    $labels = array(
        'name' => _x('Stati prodotti', 'taxonomy general name', 'e-vindemus'),
        'singular_name' => _x('Stato prodotto', 'taxonomy singular name', 'e-vindemus'),
        'search_items' => __('Cerca stati prodotti', 'e-vindemus'),
        'all_items' => __('Tutti gli stati prodotti', 'e-vindemus'),
        'parent_item' => __('Stato prodotto genitore', 'e-vindemus'),
        'parent_item_colon' => __('Stato prodotto genitore:', 'e-vindemus'),
        'edit_item' => __('Modifica stato prodotto', 'e-vindemus'),
        'update_item' => __('Aggiorna stato prodotto', 'e-vindemus'),
        'add_new_item' => __('Aggiungi nuovo stato prodotto', 'e-vindemus'),
        'new_item_name' => __('Nome nuovo stato prodotto', 'e-vindemus'),
        'menu_name' => __('Stati prodotti', 'e-vindemus'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'stato-prodotto'),
    );

    register_taxonomy('stato_prodotto', array('prodotto'), $args);

}

// add_action('admin_init', 'dci_register_cateorie_prodotti');

// function dci_register_cateorie_prodotti() {
//     $prefix ='dci_categoria_prodotto_';

//     $cmb_categoria_prodotto = new_cmb2_box(array(
//         'id' => $prefix . 'metabox',
//         'title' => __('Impostazioni categoria prodotto', 'e-vindemus'),
//         'object_types' => array('categoria_prodotto'),
//     ));

//     $cmb_categoria_prodotto->add_field(array(
//         'name' => __('Colore categoria', 'e-vindemus'),
//         'id' => $prefix . 'colore',
//         'type' => 'colorpicker',
//     ));

//     // Genitore della categoria prodotto

//     $cmb_categoria_prodotto->add_field(array(
//         'name' => __('Categoria genitore', 'e-vindemus'),
//         'id' => $prefix . 'categoria_genitore',
//         'type' => 'select',
//         'options_cb' => 'dci_get_categorie_prodotti_options',
//     ));

// }



?>