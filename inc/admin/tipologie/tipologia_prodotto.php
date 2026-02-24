<?php

/* 
    Definisco la tipologia "Prodotto". Questa tipologia sarà utilizzata per gestire i prodotti del nostro e-commerce.
*/

add_action('init', 'dci_register_tipologia_prodotto');

function dci_register_tipologia_prodotto() {
    $labels = array(
        'name'               => _x('Prodotti', 'post type general name', 'e-vindemus'),
        'singular_name'      => _x('Prodotto', 'post type singular name', 'e-vindemus'),
        'menu_name'         => _x('Prodotti', 'admin menu', 'e-vindemus'),
        'name_admin_bar'    => _x('Prodotto', 'add new on admin bar', 'e-vindemus'),
        'add_new'           => _x('Aggiungi Nuovo', 'prodotto', 'e-vindemus'),
        'add_new_item'      => __('Aggiungi Nuovo Prodotto', 'e-vindemus'),
        'new_item'          => __('Nuovo Prodotto', 'e-vindemus'),
        'edit_item'         => __('Modifica Prodotto', 'e-vindemus'),
        'view_item'         => __('Visualizza Prodotto', 'e-vindemus'),
        'all_items'         => __('Tutti i Prodotti', 'e-vindemus'),
        'search_items'      => __('Cerca Prodotti', 'e-vindemus'),
        'parent_item_colon' => __('Prodotti Genitore:', 'e-vindemus'),
        'not_found'         => __('Nessun prodotto trovato.', 'e-vindemus'),
        'not_found_in_trash'=> __('Nessun prodotto trovato nel cestino.', 'e-vindemus')
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'prodotto'),
        'capability_type'    => 'post',
        'has_archive'       => true,
        'hierarchical'      => false,
        'menu_position'     => 5,
        // Aggiungo il supporto per l'immagine in evidenza
        'supports'          => array('title', 'editor', 'thumbnail'),
        'menu_icon'     => 'dashicons-products', // Icona del menu (puoi scegliere un'icona diversa da https://developer.wordpress.org/resource/dashicons/#cart)
    );

    register_post_type('prodotto', $args);
}

/*
    Aggiunfo i campi relativi alla tipologia "Prodotto". In questo caso, aggiungo un campo per il prezzo del prodotto.
*/

add_action('cmb2_admin_init', 'dci_add_prodotto_metaboxes');

function dci_add_prodotto_metaboxes() {
    if (!function_exists('new_cmb2_box')) {
        return;
    }

    $prefix = 'dci_prodotto_';

    // Aggiungo un metabox per il prezzo del prodotto

    $cmb_info = new_cmb2_box(array(
        'id'            => $prefix . 'Informazioni Prodotto',
        'title'         => __('Dettagli Prodotto', 'e-vindemus'),
        'object_types'  => array('prodotto'), // Solo per la tipologia "Prodotto"
        'context'       => 'normal', // Posizione normale (non laterale)
        'priority'      => 'high',
    ));

    $cmb_info->add_field(array(
        'id'   => $prefix . 'descrizione_breve',
        'name' => "Descrizione breve",
        'desc' => "Inserisci una breve descrizione del prodotto (max 255 caratteri).",
        'type' => 'textarea',
        'attributes'    => array(
            'maxlength'  => '255',
            'required'    => 'required'
        ),
        
    ));
   
    $cmb_info->add_field(array(
        'id'   => $prefix . 'prezzo',
        'name' => __('Prezzo', 'e-vindemus'),
        'desc' => __('Inserisci il prezzo del prodotto in formato decimale (es. 19.99).', 'e-vindemus'),
        'type' => 'text_money',
        'before_field' => '€',
        'attributes'    => array(
            'required'    => 'required',
            'pattern'     => '\d+(\.\d{2})?', // Validazione per formato prezzo (es. 19.99)
            'title'       => 'Inserisci un prezzo valido (es. 19.99)',
            'min'         => '0.99' // Prezzo minimo accettabile fissato a 0.99€
        ),
    ));

    // Aggiungo un metabox per la disponibilità del prodotto
    // $cmb_disponibilita = new_cmb2_box(array(
    //     'id'            => $prefix . 'Disponibilità Prodotto',
    //     'title'         => __('Disponibilità Prodotto', 'e-vindemus'),
    //     'object_types'  => array('prodotto'), // Solo per la tipologia "Prodotto"
    // ));

    // Sezione info aggiuntiva al prodotto ('Specifiche Tecniche', 'Descrizioni', 'FAQ', 'Recesso e Garanzia', 'Altre Informazioni')
    
    $cmb_extra = new_cmb2_box(array(
        'id'            => $prefix . 'Informazioni Extra Prodotto',
        'title'         => __('Informazioni Extra Prodotto', 'e-vindemus'),
        'object_types'  => array('prodotto'), // Solo per la tipologia "Prodotto"
    ));

    $cmb_extra->add_field(array(
        'id'   => $prefix . 'specifiche_tecniche',
        'name' => __('Specifiche Tecniche', 'e-vindemus'),
        'desc' => __('Inserisci le specifiche tecniche del prodotto.', 'e-vindemus'),
        'type' => 'textarea',
        'attributes'    => array(
            'rows' => '5',
            'required'    => 'required'
        )
    ));

    $cmb_extra->add_field(array(
        'id'   => $prefix . 'descrizioni',
        'name' => __('Descrizioni', 'e-vindemus'),
        'desc' => __('Inserisci una descrizione dettagliata del prodotto.', 'e-vindemus'),
        'type' => 'textarea',
        'attributes'    => array(
            'rows' => '5',
            'required'    => 'required'
        )
    ));

    // CAMPO DA RIVEDERE: per ora non è necessario, ma in futuro potrebbe essere utile per gestire le domande frequenti relative al prodotto (FAQ)
    // $cmb_extra->add_field(array(
    //     'id'   => $prefix . 'faq',
    //     'name' => __('FAQ', 'e-vindemus'),
    //     'desc' => __('Inserisci le domande frequenti relative al prodotto.', 'e-vindemus'),
    //     'type' => 'textarea',
    //     'attributes'    => array(
    //         'rows' => '5',
    //         'required'    => 'required'
    //     )
    // ));


    $cmb_extra->add_field(array(
        'id'   => $prefix . 'recesso_garanzia',
        'name' => __('Recesso e Garanzia', 'e-vindemus'),
        'desc' => __('Inserisci le informazioni relative al recesso e alla garanzia del prodotto. Questa parte è fondamentale per non incorere in sanzioni legali.', 'e-vindemus'),
        'type' => 'textarea',
        'attributes'    => array(
            'rows' => '5',
            'required'    => 'required'
        )
    ));

    $cmb_extra->add_field(array(
        'id'   => $prefix . 'altre_informazioni',
        'name' => __('Altre Informazioni', 'e-vindemus'),
        'desc' => __('Inserisci eventuali altre informazioni rilevanti relative al prodotto.', 'e-vindemus'),
        'type' => 'textarea',
        'attributes'    => array(
            'rows' => '5',
            'required'    => 'required'
        )
    ));

    // Sezione Relativo alla gestione dello stock del prodotto (es. quantità disponibile, gestione magazzino, ecc.)

    $cmb_stock = new_cmb2_box(array(
        'id'            => $prefix . 'Stock Prodotto',
        'title'         => __('Stock Prodotto', 'e-vindemus'),
        'object_types'  => array('prodotto'), // Solo per la tipologia "Prodotto"
        'context'       => 'normal', // Posizione normale (non laterale)
        'priority'      => 'default',
    ));

    $cmb_stock->add_field(array(
        'id'   => $prefix . 'quantita_disponibile',
        'name' => __('Quantità Disponibile', 'e-vindemus'),
        'desc' => __('Inserisci la quantità disponibile in stock per questo prodotto.', 'e-vindemus'),
        'type' => 'text_small',
        'attributes'    => array(
            'required'    => 'required',
            'pattern'     => '\d+', // Validazione per numeri interi
            'title'       => 'Inserisci una quantità valida (numeri interi)',
            'min'         => '0' // Quantità minima accettabile fissata a 0
        )
    ));

    // Sezione laterale per la gestione della tipologia del prodotto (es. vino, accessorio, ecc.)
    $cmb_tipo = new_cmb2_box(array(
        'id'            => $prefix . 'Tipo Prodotto',
        'title'         => __('Tipo Prodotto', 'e-vindemus'),
        'object_types'  => array('prodotto'), // Solo per la tipologia "Prodotto"
        'context'       => 'side', // Posizione laterale
        'priority'      => 'default',
    ));

    // DA IMPLEMENTARE: aggiungere un campo di selezione per la tipologia del prodotto (es. vino, accessorio, ecc.) in modo da poter categorizzare i prodotti in base alla loro tipologia specifica. Questo potrebbe essere implementato come un campo di selezione a discesa (dropdown) o come un set di checkbox, a seconda delle esigenze specifiche del progetto.


    // $cmb_tipo->add_field(array(
    //     'id'   => $prefix . 'tipo_prodotto',
    //     'name' => __('Tipo Prodotto', 'e-vindemus'),
    //     'desc' => __('Seleziona la tipologia del prodotto (es. vino, accessorio, ecc.).', 'e-vindemus'),
    //     'type' => 'select',
    //     'options' => array(
    //         'vino' => __('Vino', 'e-vindemus'),
    //         'accessorio' => __('Accessorio', 'e-vindemus'),
    //         'altro' => __('Altro', 'e-vindemus'),
    //     ),
    //     'attributes'    => array(
    //         'required'    => 'required'
    //     )
    // ));

    // DA IMPLEMENTARE aggiungere la possibilità di fare in modo che lo stato del prodotto si aggiorni automaticamente in base alla quantità disponibile in stock (es. se la quantità è 0, lo stato del prodotto diventa "Esaurito", se la quantità è maggiore di 0, lo stato del prodotto diventa "Disponibile"). Questo potrebbe essere implementato tramite un hook che si attiva quando viene salvato il prodotto e aggiorna automaticamente lo stato in base alla quantità disponibile.
}

// Aggiugere degli script che verificano se i campi sono compilati in modo giusto.
