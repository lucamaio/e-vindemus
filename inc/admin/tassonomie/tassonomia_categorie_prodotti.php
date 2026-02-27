<?php
/**
 * Definisce la tassonomia "Categorie prodotti" per il custom post type "Prodotto".
 */

add_action('init', 'evindemus_register_tassonomia_categorie_prodotti');

function evindemus_register_tassonomia_categorie_prodotti() {
    $labels = array(
        'name' => _x('Categorie prodotti', 'taxonomy general name', 'e-vindemus'),
        'singular_name' => _x('Categoria prodotto', 'taxonomy singular name', 'e-vindemus'),
        'search_items' => __('Cerca categorie prodotti', 'e-vindemus'),
        'all_items' => __('Tutte le categorie prodotti', 'e-vindemus'),
        'parent_item' => __('Categoria prodotto genitore', 'e-vindemus'),
        'parent_item_colon' => __('Categoria prodotto genitore:', 'e-vindemus'),
        'edit_item' => __('Modifica categoria prodotto', 'e-vindemus'),
        'update_item' => __('Aggiorna categoria prodotto', 'e-vindemus'),
        'add_new_item' => __('Aggiungi nuova categoria prodotto', 'e-vindemus'),
        'new_item_name' => __('Nome nuova categoria prodotto', 'e-vindemus'),
        'menu_name' => __('Categorie prodotti', 'e-vindemus'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'categoria-prodotto'),
    );

    register_taxonomy('categoria_prodotto', array('prodotto'), $args);

}

add_action('admin_init', 'dci_register_cateorie_prodotti');

function dci_register_cateorie_prodotti() {
    $prefix ='dci_categoria_prodotto_';

    $cmb_categoria_prodotto = new_cmb2_box(array(
        'id' => $prefix . 'metabox',
        'title' => __('Impostazioni categoria prodotto', 'e-vindemus'),
        'object_types' => array('categoria_prodotto'),
    ));

    $cmb_categoria_prodotto->add_field(array(
        'name' => __('Colore categoria', 'e-vindemus'),
        'id' => $prefix . 'colore',
        'type' => 'colorpicker',
    ));

    // Genitore della categoria prodotto

    $cmb_categoria_prodotto->add_field(array(
        'name' => __('Categoria genitore', 'e-vindemus'),
        'id' => $prefix . 'categoria_genitore',
        'type' => 'select',
        'options_cb' => 'dci_get_categorie_prodotti_options',
    ));

}

?>