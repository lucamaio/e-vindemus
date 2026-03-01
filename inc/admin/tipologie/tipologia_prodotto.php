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

    /**
     *  Campi sezione Principale
     */

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
        'id'              => $prefix . 'prezzo',
        'name'            => __('Prezzo', 'e-vindemus'),
        'desc'            => __('Inserisci il prezzo del prodotto in formato decimale (es. 19.99 o 19,99).', 'e-vindemus'),
        'type'            => 'text',
        'before_field'    => '€',
        'attributes'      => array(
            'required'  => 'required',
            'inputmode' => 'decimal',
            'pattern'   => '^\d+([\.,]\d{1,2})?$',
            'title'     => __('Inserisci un valore valido (es. 19.99 o 19,99).', 'e-vindemus'),
        ),
        'sanitization_cb' => 'dci_sanitize_prezzo_prodotto',
        'escape_cb'       => 'dci_escape_prezzo_prodotto',
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
    // Sezione rimmossa in quanto attualmente non viene gestita la quantità disponibile in stock per i prodotti, ma potrebbe essere implementata in futuro se necessario. In questo caso, si potrebbe aggiungere un campo per la quantità disponibile in stock e implementare una logica per aggiornare automaticamente lo stato del prodotto (es. "Disponibile", "Esaurito") in base alla quantità disponibile.  
    // $cmb_stock = new_cmb2_box(array(
    //     'id'            => $prefix . 'Stock Prodotto',
    //     'title'         => __('Stock Prodotto', 'e-vindemus'),
    //     'object_types'  => array('prodotto'), // Solo per la tipologia "Prodotto"
    //     'context'       => 'normal', // Posizione normale (non laterale)
    //     'priority'      => 'default',
    // ));

    // $cmb_stock->add_field(array(
    //     'id'   => $prefix . 'quantita_disponibile',
    //     'name' => __('Quantità Disponibile', 'e-vindemus'),
    //     'desc' => __('Inserisci la quantità disponibile in stock per questo prodotto.', 'e-vindemus'),
    //     'type' => 'text_small',
    //     'attributes'    => array(
    //         'pattern'     => '\d+', // Validazione per numeri interi
    //         'title'       => 'Inserisci una quantità valida (numeri interi)',
    //         'min'         => '0' // Quantità minima accettabile fissata a 0
    //     )
    // ));

    /**
     *  Campi sezione laterale
    */

    // Sezione laterale relativa alla gestione del posizionamento evidenziato del prodotto (es. homepage, offerte, ecc.)

    $cmb_posizione_evidenziata = new_cmb2_box(array(
        'id'            => $prefix . 'Posizione Evidenziata Prodotto',
        'title'         => __('Posizione Evidenziata Prodotto', 'e-vindemus'),
        'object_types'  => array('prodotto'), // Solo per la tipologia "Prodotto"
        'context'       => 'side', // Posizione laterale
        'priority'      => 'high',
    ));

    $cmb_posizione_evidenziata->add_field(array(
        'id'   => $prefix . 'posizione_evidenziata',
        'name' => __('Posizione Evidenziata', 'e-vindemus'),
        'desc' => __('Seleziona la posizione in cui evidenziare il prodotto (es. homepage, offerte, ecc.).<br>Se non selezioni niente allora non verà messa in evidenza.', 'e-vindemus'),
        'type' => 'taxonomy_multicheck_hierarchical',
        'taxonomy' => 'posizione_evidenziata', // Associa questo campo alla tassonomia "Posizione evidenziata"
        'show_option_none' => false,
        'remove_default' => 'true'
    ));

    // Data inizio evidenza e data fine

    $cmb_posizione_evidenziata->add_field(array(
        'id' => $prefix.'data_inizio_evidenza',
        'name' => __('Data Inizio: ','e-vindemus'),
        'type'    => 'text_date_timestamp',
        'date_format' => 'd-m-Y'
    ));

     $cmb_posizione_evidenziata->add_field(array(
        'id' => $prefix.'data_fine_evidenza',
        'name' => __('Data Fine: ','e-vindemus'),
        'type'    => 'text_date_timestamp',
        'date_format' => 'd-m-Y'
    ));

    $cmb_posizione_evidenziata->add_field(array(
        'desc' => __("Attenzione, se il prodotto evidenziato è segnato come NON DISPONIBILE, ESAURiTO allora non verà visualizzato. Se Viene inserita una data di inizio e una di fine dopo tale periodo non sarà più in evidenza.")
    ));


    // Sezione laterale relativa alla gestione dello stato del prodotto (es. disponibile, esaurito, in arrivo, ecc.)

    $cmb_stato = new_cmb2_box(array(
        'id'            => $prefix . 'Stato Prodotto',
        'title'         => __('Stato Prodotto', 'e-vindemus'),
        'object_types'  => array('prodotto'), // Solo per la tipologia "Prodotto"
        'context'       => 'side', // Posizione laterale
        'priority'      => 'default',
    ));

    $cmb_stato->add_field(array(
        'id'   => $prefix . 'stato_prodotto',
        'name' => __('Stato Prodotto *', 'e-vindemus'),
        'desc' => __('Seleziona lo stato del prodotto (es. Disponibile, Esaurito, In arrivo).', 'e-vindemus'),
        'type' => 'taxonomy_radio_hierarchical',
        'taxonomy' => 'stato_prodotto', // Associa questo campo alla tassonomia "Stato prodotto"
        'show_option_none' => false,
        'remove_default' => 'true',
        'attributes'    => array(
            'required'    => 'required'
        ),
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

    $cmb_tipo->add_field(array(
        'id'   => $prefix . 'tipo_prodotto',
        'name' => __('Tipo Prodotto *', 'e-vindemus'),
        'desc' => __('Seleziona la tipologia del prodotto (es. T-shirt, maglietta, ecc.).', 'e-vindemus'),
        'type' => 'taxonomy_multicheck_hierarchical',
        'taxonomy' => 'categoria_prodotto', // Associa questo campo alla tassonomia "Categoria prodotto"
        'show_option_none' => false,
        'remove_default' => 'true'
    ));

    // Sezione laterale per la gestione dei campi relativi alla categoria Abbigliamento (es. Taglia, Colore, Materiale, ecc.)

    $cmb_categoria_abbigliamento = new_cmb2_box(array(
        'id'            => $prefix . 'Categoria Abbigliamento',
        'title'         => __('Categoria Abbigliamento', 'e-vindemus'),
        'object_types'  => array('prodotto'), // Solo per la tipologia "Prodotto"
        'context'       => 'side', // Posizione laterale
        'priority'      => 'default',
    ));

    // Campo per la selezione della taglia del prodotto (es. S, M, L, XL, ecc.)

    $cmb_categoria_abbigliamento->add_field(array(
        'id'   => $prefix . 'taglia_prodotto',
        'name' => __('Taglia Prodotto', 'e-vindemus'),
        'desc' => __('Seleziona la taglia del prodotto (es. S, M, L, XL, ecc.).', 'e-vindemus'),
        'type' => 'taxonomy_multicheck_hierarchical',
        'taxonomy' => 'taglia', // Associa questo campo alla tassonomia "Taglia"
        'show_option_none' => false,
        'remove_default' => 'true'
        ),
    );

    // Campo per la selezione della taglia di scarpe del prodotto (es. 38, 39, 40, ecc.)

    $cmb_categoria_abbigliamento->add_field(array(
        'id'   => $prefix . 'taglia_scarpe_prodotto',
        'name' => __('Taglia Scarpe Prodotto', 'e-vindemus'),
        'desc' => __('Seleziona la taglia di scarpe del prodotto (es. 38, 39, 40, ecc.).', 'e-vindemus'),
        'type' => 'taxonomy_multicheck_hierarchical',
        'taxonomy' => 'taglia_scarpe', // Associa questo campo alla tassonomia "Taglia Scarpe"
        'show_option_none' => false,
        'remove_default' => 'true'
        ),
    );

    // Campo per la selezione del sesso del prodotto (es. Uomo, Donna, Unisex)

    $cmb_categoria_abbigliamento->add_field(array(
        'id'   => $prefix . 'sesso_prodotto',
        'name' => __('Sesso Prodotto', 'e-vindemus'),
        'desc' => __('Seleziona il sesso del prodotto (es. Uomo, Donna, Unisex).', 'e-vindemus'),
        'type' => 'taxonomy_multicheck_hierarchical',
        'taxonomy' => 'sesso', // Associa questo campo alla tassonomia "Sesso"
        'show_option_none' => false,
        'remove_default' => 'true'
        ),
    );

    // Campo per la selezione del materiale del prodotto (es. Cotone, Poliestere, ecc.)

    $cmb_categoria_abbigliamento->add_field(array(
        'id'   => $prefix . 'materiale_prodotto',
        'name' => __('Materiale Prodotto', 'e-vindemus'),
        'desc' => __('Seleziona il materiale del prodotto (es. Cotone, Poliestere, ecc.).', 'e-vindemus'),
        'type' => 'taxonomy_multicheck_hierarchical',
        'taxonomy' => 'materiale', // Associa questo campo alla tassonomia "Materiale"
        'show_option_none' => false,
        'remove_default' => 'true'
        ),
    );

    // Campo per la selezione del colore del prodotto (es. Rosso, Blu, Verde, ecc.)
    $cmb_categoria_abbigliamento->add_field(array(
        'id'   => $prefix . 'colore_prodotto',
        'name' => __('Colore Prodotto', 'e-vindemus'),
        'desc' => __('Seleziona il colore del prodotto (es. Rosso, Blu, Verde, ecc.).', 'e-vindemus'),
        'type' => 'taxonomy_multicheck_hierarchical',
        'taxonomy' => 'colore', // Associa questo campo alla tassonomia "Colore"
        'show_option_none' => false,
        'remove_default' => 'true'
        ),
    );



    // DA IMPLEMENTARE aggiungere la possibilità di fare in modo che lo stato del prodotto si aggiorni automaticamente in base alla quantità disponibile in stock (es. se la quantità è 0, lo stato del prodotto diventa "Esaurito", se la quantità è maggiore di 0, lo stato del prodotto diventa "Disponibile"). Questo potrebbe essere implementato tramite un hook che si attiva quando viene salvato il prodotto e aggiorna automaticamente lo stato in base alla quantità disponibile.
}

/**
 * Script admin: visibilità dinamica campi abbigliamento nel CPT prodotto.
 * - Mostra "Categoria Abbigliamento" solo se è selezionata una categoria figlia di Abbigliamento.
 * - Se è selezionata una categoria Scarpe, mostra taglia scarpe e nasconde taglia prodotto.
 */
function dci_prodotto_categoria_abbigliamento_visibility_script() {
    if (!is_admin()) {
        return;
    }

    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->post_type !== 'prodotto') {
        return;
    }

    $abbigliamento_ids = [];
    $abbigliamento_term = get_term_by('slug', 'abbigliamento', 'categoria_prodotto');
    if (!$abbigliamento_term || is_wp_error($abbigliamento_term)) {
        $abbigliamento_term = get_term_by('name', 'Abbigliamento', 'categoria_prodotto');
    }

    if ($abbigliamento_term instanceof WP_Term) {
        $children = get_term_children((int) $abbigliamento_term->term_id, 'categoria_prodotto');
        if (is_wp_error($children)) {
            $children = [];
        }
        $abbigliamento_ids = array_values(array_unique(array_merge([(int) $abbigliamento_term->term_id], array_map('intval', $children))));
    }

    $scarpe_ids = [];
    if (!empty($abbigliamento_ids)) {
        $abbigliamento_terms = get_terms([
            'taxonomy'   => 'categoria_prodotto',
            'hide_empty' => false,
            'include'    => $abbigliamento_ids,
        ]);

        if (!is_wp_error($abbigliamento_terms) && !empty($abbigliamento_terms)) {
            foreach ($abbigliamento_terms as $term) {
                if (!$term instanceof WP_Term) {
                    continue;
                }

                $slug = sanitize_title((string) $term->slug);
                $name = sanitize_title((string) $term->name);
                if (false !== strpos($slug, 'scarp') || false !== strpos($name, 'scarp')) {
                    $scarpe_ids[] = (int) $term->term_id;
                }
            }
        }
    }
    ?>
    <script>
    (function () {
        var abbigliamentoIds = <?php echo wp_json_encode(array_values(array_unique(array_map('intval', $abbigliamento_ids)))); ?>;
        var scarpeIds = <?php echo wp_json_encode(array_values(array_unique(array_map('intval', $scarpe_ids)))); ?>;

        var toArray = function (items) {
            return Array.prototype.slice.call(items || []);
        };

        var toSafeText = function (value) {
            return String(value || '').toLowerCase().trim();
        };

        var getSelectedTipoIds = function () {
            var inputs = document.querySelectorAll(
                '.cmb2-id--dci-prodotto-tipo-prodotto input[type="checkbox"]:checked,' +
                '.cmb2-id--dci-prodotto-tipo-prodotto input[type="radio"]:checked,' +
                'input[name*="_dci_prodotto_tipo_prodotto"][type="checkbox"]:checked,' +
                'input[name*="_dci_prodotto_tipo_prodotto"][type="radio"]:checked'
            );

            return toArray(inputs).map(function (input) {
                return parseInt(input.value || '0', 10);
            }).filter(function (id) {
                return !Number.isNaN(id) && id > 0;
            });
        };

        var getCategoriaAbbigliamentoBox = function () {
            var byId = document.getElementById('_dci_prodotto_Categoria Abbigliamento');
            if (byId) {
                return byId;
            }

            var postboxes = toArray(document.querySelectorAll('.postbox'));
            return postboxes.find(function (box) {
                var title = box.querySelector('.hndle, .cmb2-metabox-title, h2 span');
                return title && toSafeText(title.textContent).indexOf('categoria abbigliamento') !== -1;
            }) || null;
        };

        var getTagliaRow = function () {
            return document.querySelector(
                '.cmb2-id--dci-prodotto-taglia-prodotto,' +
                '[class*="cmb2-id-_dci_prodotto_taglia_prodotto"],' +
                '[class*="cmb2-id--dci-prodotto-taglia-prodotto"]'
            );
        };

        var getTagliaScarpeRow = function () {
            return document.querySelector(
                '.cmb2-id--dci-prodotto-taglia-scarpe-prodotto,' +
                '[class*="cmb2-id-_dci_prodotto_taglia_scarpe_prodotto"],' +
                '[class*="cmb2-id--dci-prodotto-taglia-scarpe-prodotto"]'
            );
        };

        var setVisible = function (element, visible) {
            if (!element) {
                return;
            }
            element.style.display = visible ? '' : 'none';
        };

        var syncVisibility = function () {
            var selectedIds = getSelectedTipoIds();
            var isAbbigliamento = selectedIds.some(function (id) {
                return abbigliamentoIds.indexOf(id) !== -1;
            });
            var isScarpe = selectedIds.some(function (id) {
                return scarpeIds.indexOf(id) !== -1;
            });

            var box = getCategoriaAbbigliamentoBox();
            var tagliaRow = getTagliaRow();
            var tagliaScarpeRow = getTagliaScarpeRow();

            setVisible(box, isAbbigliamento);

            if (!isAbbigliamento) {
                setVisible(tagliaRow, false);
                setVisible(tagliaScarpeRow, false);
                return;
            }

            setVisible(tagliaScarpeRow, isScarpe);
            setVisible(tagliaRow, !isScarpe);
        };

        document.addEventListener('change', function (event) {
            if (!event.target) {
                return;
            }

            var target = event.target;
            var isTipoField = false;

            if (target.name && target.name.indexOf('_dci_prodotto_tipo_prodotto') !== -1) {
                isTipoField = true;
            }

            if (!isTipoField) {
                var row = target.closest('.cmb-row');
                if (row && row.className && row.className.indexOf('tipo-prodotto') !== -1) {
                    isTipoField = true;
                }
            }

            if (isTipoField) {
                syncVisibility();
            }
        });

        document.addEventListener('DOMContentLoaded', syncVisibility);
        window.setTimeout(syncVisibility, 180);
        window.setTimeout(syncVisibility, 500);
    })();
    </script>
    <?php
}
add_action('admin_footer-post.php', 'dci_prodotto_categoria_abbigliamento_visibility_script');
add_action('admin_footer-post-new.php', 'dci_prodotto_categoria_abbigliamento_visibility_script');

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

/**
 * Sanifica il prezzo prodotto mantenendo il valore decimale con punto.
 *
 * @param mixed $value
 * @return string
 */
function dci_sanitize_prezzo_prodotto($value) {
    if (!is_scalar($value)) {
        return '';
    }

    $value = trim((string) $value);

    if ($value === '') {
        return '';
    }

    $value = str_replace(',', '.', $value);

    if (!preg_match('/^\d+(\.\d{1,2})?$/', $value)) {
        return '';
    }

    return $value;
}

/**
 * Escape del prezzo prodotto nei campi admin.
 *
 * @param mixed $value
 * @return string
 */
function dci_escape_prezzo_prodotto($value) {
    return esc_attr((string) $value);
}
