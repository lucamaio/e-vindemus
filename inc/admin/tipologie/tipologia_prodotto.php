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
        'label'             => __('Prodotto', 'e-vindemus'),
        'labels'             => $labels,
        'supports'          => array('title', 'editor', 'author'),
        'hierarchical'      => false,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'prodotto'),
        'capability_type'    => array('prodotto', 'prodotti'),
        'capabilities' => array(
            'edit_post' => 'edit_prodotto',
            'read_post' => 'read_prodotto',
            'delete_post' => 'delete_prodotto',
            'edit_posts' => 'edit_prodotti',
            'edit_others_posts' => 'edit_others_prodotti',
            'publish_posts' => 'publish_prodotti',
            'read_private_posts' => 'read_private_prodotti',
            'delete_posts' => 'delete_prodotti',
            'delete_private_posts' => 'delete_private_prodotti',
            'delete_published_posts' => 'delete_published_prodotti',
            'delete_others_posts' => 'delete_others_prodotti',
            'edit_private_posts' => 'edit_private_prodotti',
            'edit_published_posts' => 'edit_published_prodotti',
            'create_posts' => 'create_prodotti'
        ),
        'has_archive'       => false,
        'menu_position'     => 5,
        'map_meta_cap'       => true, 
        'menu_icon'     => 'dashicons-products', // Icona del menu (puoi scegliere un'icona diversa da https://developer.wordpress.org/resource/dashicons/#cart),
        'description'     => __('Tipologia per gestire i prodotti del nostro e-commerce.', 'e-vindemus')
    );

    register_post_type('prodotto', $args);

    remove_post_type_support('prodotto', 'editor'); // Rimuovo l'editor classico per i prodotti, in quanto utilizzeremo i campi custom per gestire le informazioni del prodotto.
}

/*
    Aggiunfo i campi relativi alla tipologia "Prodotto". In questo caso, aggiungo un campo per il prezzo del prodotto.
*/

add_action('cmb2_admin_init', 'dci_add_prodotto_metaboxes');

function dci_add_prodotto_metaboxes() {
    if (!function_exists('new_cmb2_box')) {
        return;
    }

    $prefix = '_dci_prodotto_';

    // Metabox per le immagini del prodotto (es. galleria immagini, immagine in evidenza, ecc.)

    $cmb_imgs = new_cmb2_box(array(
        'id'            => $prefix . 'Immagini Prodotto',
        'title'         => __('Immagini Prodotto', 'e-vindemus'),
        'object_types'  => array('prodotto'), // Solo per la tipologia "Prodotto"
    ));

    $cmb_imgs->add_field(array(
        'name' => 'Immagine in evidenza del prodotto *',
        'id'   => $prefix . 'immagine_evidenza',
        'type' => 'file',
        'options' => array(
            'url' => false, // Nascondi il campo URL
        ),
        'attributes'    => array(
            'required'    => 'required',
        ),
        'preview_size' => array( 100, 100 ),
        'query_args' => array( 'type' => 'image' ),
    ));

    $cmb_imgs->add_field(array(
        'name' => 'Immagini del prodotto ',
        'id'   => $prefix . 'galleria_immagini',
        'type' => 'file_list',
        'options' => array(
            'url' => false, // Nascondi il campo URL
        ),
        'preview_size' => array( 100, 100 ),
        'query_args' => array( 'type' => 'image' ),
    ));

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
            'required'    => 'required', 
            'width' => '100%',
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
        'name' => __('Specifiche Tecniche *', 'e-vindemus'),
        'desc' => __('Inserisci le specifiche tecniche del prodotto.', 'e-vindemus'),
        'type' => 'wysiwyg',
        'attributes'    => array(
            'required'    => 'required',
            'width' => '100%',
        ),
        'options' => array(
            'textarea_rows' => 10,
            'media_buttons' => false, // Nascondi il pulsante per aggiungere media
            'teeny'         => false, // Usa la versione completa dell'editor
        ),
    ));

    $cmb_extra->add_field(array(
        'id'   => $prefix . 'descrizioni',
        'name' => __('Descrizioni *', 'e-vindemus'),
        'desc' => __('Inserisci una descrizione dettagliata del prodotto.', 'e-vindemus'),
        'type' => 'wysiwyg',
        'attributes'    => array(
            'required'    => 'required',
            'width' => '100%',
            'min-length' => '20', // Lunghezza minima della descrizione dettagliata
        ),
        'options' => array(
            'textarea_rows' => 10,
            'media_buttons' => false, // Nascondi il pulsante per aggiungere media
            'teeny'         => false, // Usa la versione completa dell'editor
        ),
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
        'name' => __('Recesso e Garanzia *', 'e-vindemus'),
        'desc' => __('Inserisci le informazioni relative al recesso e alla garanzia del prodotto. Questa parte è fondamentale per non incorere in sanzioni legali.', 'e-vindemus'),
        'type' => 'wysiwyg',
        'attributes'    => array(
            'required'    => 'required',
            'width' => '100%',
        ),
        'options' => array(
            'textarea_rows' => 5,
            'media_buttons' => false, // Nascondi il pulsante per aggiungere media
            'teeny'         => false, // Usa la versione completa dell'editor
        ),
    ));

    $cmb_extra->add_field(array(
        'id'   => $prefix . 'altre_informazioni',
        'name' => __('Altre Informazioni', 'e-vindemus'),
        'desc' => __('Inserisci eventuali altre informazioni rilevanti relative al prodotto.', 'e-vindemus'),
        'type' => 'wysiwyg',
        'attributes'    => array(
            'width' => '100%',
        ),
        'options' => array(
            'textarea_rows' => 10,
            'media_buttons' => false, // Nascondi il pulsante per aggiungere media
            'teeny'         => false, // Usa la versione completa dell'editor
        ),
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


/**
 * Valorizzo il post content in base al contenuto dei campi custom
 * @param $data
 * @return mixed
 */

add_filter( 'wp_insert_post_data', 'dci_prodotto_set_post_content', 99, 2 );
function dci_prodotto_set_post_content( $data ) {

    if($data['post_type'] == 'prodotto') {

        $descrizione_breve = '';
        if (isset($_POST['_dci_prodotto_descrizione_breve'])) {
            $descrizione_breve = $_POST['_dci_prodotto_descrizione_breve'];
        }

        $descrizione_estesa = '';
        if (isset($_POST['_dci_prodotto_descrizioni'])) {
            $descrizione_estesa = $_POST['_dci_prodotto_descrizioni'];
        }

        $prezzo = '';
        if (isset($_POST['_dci_prodotto_prezzo'])) {
            $prezzo = $_POST['_dci_prodotto_prezzo'];
        }


        $content = $descrizione_breve.'<br>'.$descrizione_estesa.'<br>Prezzo: '.$prezzo;

        $data['post_content'] = $content;
    }

    return $data;
}

/**
 * Rende più prevedibili i controlli permessi per i prodotti.
 *
 * Alcuni plugin di gestione ruoli assegnano solamente i permessi principali
 * (es. edit_prodotti) e non quelli derivati da stato/autore
 * (es. edit_published_prodotti, edit_others_prodotti). In questo modo,
 * l'utente riesce a creare il prodotto ma non ad aggiornarlo una volta salvato.
 *
 * Qui mappiamo i meta-cap del singolo prodotto ai permessi principali,
 * evitando blocchi in aggiornamento quando il ruolo è già abilitato alla
 * gestione della tipologia.
 */
add_filter('map_meta_cap', 'dci_map_meta_cap_prodotto', 10, 4);
function dci_map_meta_cap_prodotto($caps, $cap, $user_id, $args) {
    if (empty($args[0]) || !in_array($cap, array('edit_prodotto', 'delete_prodotto', 'read_prodotto'), true)) {
        return $caps;
    }

    $post = get_post((int) $args[0]);

    if (!$post || $post->post_type !== 'prodotto') {
        return $caps;
    }

    if ($cap === 'edit_prodotto') {
        return array('edit_prodotti');
    }

    if ($cap === 'delete_prodotto') {
        return array('delete_prodotti');
    }

    if ($cap === 'read_prodotto') {
        return array('read');
    }

    return $caps;
}
