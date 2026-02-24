<?php

function dci_register_homepage_options() {
   
    $prefix = '';

    /*
        Opzioni homepage
    */

    $args = array(
        'id'           => $prefix . 'dci_homepage_options',
        'title'        => 'Opzioni homepage',
        'object_types' => array('page'), // Post type a cui associare le opzioni
        'option_key'   => 'dci_homepage_options', // Chiave per salvare le opzioni nel database
        'tab_group'    => 'dci_options',
        'tab_title'    => __('Home Page', "design_comuni_italia"),	
    );


    // Aggiungo i campi per le opzioni della homepage

    $home_options = new_cmb2_box( $args );
    
    // Campo Nome sito
    $home_options->add_field( array(
        'name' => 'Titolo sito *',
        'id'   => $prefix . 'home_site_title',
        'type' => 'text',
        'default' => 'E-vindemus',
        'attributes' => array(
            'required' => 'required',
        ),
    ) );

    // Campo motto sito
    $home_options->add_field( array(
        'name' => 'Motto sito',
        'id'   => $prefix . 'home_site_motto',
        'type' => 'text',
        'default' => 'Il tuo negozio di vini online',
    ) );

    // Campo Immagini carousel
    $home_options->add_field( array(
        'name' => 'Immagini carousel',
        'id'   => $prefix . 'home_carousel_images',
        'type' => 'file_list',
        'options' => array(
            'url' => false, // Nascondi il campo URL
        ),
        'preview_size' => array( 100, 100 ),
        'query_args' => array( 'type' => 'image' ),
    ) );

    // Campo Articoli in evidenza (relazione con post)
    // $home_options->add_field( array(
    //     'name' => 'Articoli in evidenza',
    //     'id'   => $prefix . 'home_featured_posts',
    //     'type' => 'post_search_text',
    //     'post_type' => array('post'), // Solo post standard
    //     'select_type' => 'checkbox', // Permette di selezionare più articoli
    // ) );




}


?>