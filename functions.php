<?php

// Includo il file del vocabolario, che contiene funzioni per recuperare array di categorie e termini personalizzati.

require_once get_template_directory() . '/inc/vocabolario.php';

// Includo il file di attivazione del tema, che gestisce setup iniziale e bootstrap post-attivazione.

require_once get_template_directory() . '/inc/activation.php';

// includo tutte le opzioni del tema
require_once get_template_directory() . '/inc/admin/options.php';

// Includo tutte le tassonomie e tipologie del tema
require_once get_template_directory() . '/inc/admin/tassonomie.php';

require_once get_template_directory() . '/inc/admin/tipologie.php';

/**
 * Setup base del tema.
 */
function dci_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'dci_theme_setup');

/**
 * Carica assets del tema.
 */
function mio_tema_scripts() {
    $theme_version = wp_get_theme()->get('Version');
    $style_path    = get_stylesheet_directory() . '/style.css';
    $style_version = file_exists($style_path) ? filemtime($style_path) : $theme_version;

    // CSS Bootstrap
    wp_enqueue_style(
        'bootstrap-css',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
        [],
        '5.3.2'
    );

    // Font Awesome (icone header/mobile)
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
        [],
        '6.5.2'
    );

    // Stile principale del tema (child-theme safe)
    wp_enqueue_style(
        'theme-style',
        get_stylesheet_uri(),
        ['bootstrap-css', 'font-awesome'],
        $style_version
    );

    // JS Bootstrap
    wp_enqueue_script(
        'bootstrap-js',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
        [],
        '5.3.2',
        true
    );
}
add_action('wp_enqueue_scripts', 'mio_tema_scripts', 20);

/**
 * Fallback di sicurezza: se per qualche motivo non risulta enqueued, forza style.css nel <head>.
 */
function dci_force_theme_style_fallback() {
    if (!wp_style_is('theme-style', 'enqueued')) {
        wp_enqueue_style('theme-style', get_stylesheet_uri(), ['bootstrap-css']);
    }
}
add_action('wp_head', 'dci_force_theme_style_fallback', 1);

/**
 * Avvia una sessione PHP per gestire il carrello custom del tema.
 */
function ev_start_session_for_cart() {
    if (is_admin()) {
        return;
    }

    if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
        session_start();
    }
}
add_action('init', 'ev_start_session_for_cart', 1);

/**
 * Salva nel carrello in sessione il prodotto con le varianti selezionate.
 */
function ev_handle_add_to_cart_request() {
    if ('POST' !== strtoupper($_SERVER['REQUEST_METHOD'] ?? '')) {
        return;
    }

    $action = isset($_POST['ev_action']) ? sanitize_text_field(wp_unslash($_POST['ev_action'])) : '';
    if ('add_to_cart' !== $action) {
        return;
    }

    $product_id = isset($_POST['ev_product_id']) ? absint($_POST['ev_product_id']) : 0;
    if ($product_id <= 0 || 'prodotto' !== get_post_type($product_id)) {
        return;
    }

    $nonce = isset($_POST['ev_add_to_cart_nonce']) ? sanitize_text_field(wp_unslash($_POST['ev_add_to_cart_nonce'])) : '';
    if (!wp_verify_nonce($nonce, 'ev_add_to_cart_' . $product_id)) {
        return;
    }

    $variants = [];
    if (isset($_POST['ev_varianti']) && is_array($_POST['ev_varianti'])) {
        foreach ($_POST['ev_varianti'] as $variant_key => $variant_value) {
            $clean_key = sanitize_key((string) $variant_key);
            $clean_value = sanitize_text_field(wp_unslash((string) $variant_value));

            if ('' !== $clean_key && '' !== $clean_value) {
                $variants[$clean_key] = $clean_value;
            }
        }
    }

    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }

    if (!isset($_SESSION['ev_cart']) || !is_array($_SESSION['ev_cart'])) {
        $_SESSION['ev_cart'] = [];
    }

    $item_key = md5($product_id . '|' . wp_json_encode($variants));
    if (!isset($_SESSION['ev_cart'][$item_key])) {
        $_SESSION['ev_cart'][$item_key] = [
            'product_id' => $product_id,
            'qty' => 0,
            'variants' => $variants,
        ];
    }

    $_SESSION['ev_cart'][$item_key]['qty']++;

    $redirect_url = wp_get_referer();
    if (empty($redirect_url)) {
        $redirect_url = get_permalink($product_id);
    }

    $redirect_url = add_query_arg('ev_added_to_cart', '1', $redirect_url);
    wp_safe_redirect($redirect_url);
    exit;
}
add_action('template_redirect', 'ev_handle_add_to_cart_request', 1);

/**
 * Normalizza i filtri della ricerca prodotti provenienti dalla query string.
 *
 * @return array{tipo:string,prezzo_min:string,prezzo_max:string}
 */
function ev_get_search_product_filters() {
    $tipo = isset($_GET['tipo']) ? sanitize_title(wp_unslash((string) $_GET['tipo'])) : '';
    $prezzo_min = isset($_GET['prezzo_min']) ? str_replace(',', '.', sanitize_text_field(wp_unslash((string) $_GET['prezzo_min']))) : '';
    $prezzo_max = isset($_GET['prezzo_max']) ? str_replace(',', '.', sanitize_text_field(wp_unslash((string) $_GET['prezzo_max']))) : '';

    if ($prezzo_min !== '' && !is_numeric($prezzo_min)) {
        $prezzo_min = '';
    }

    if ($prezzo_max !== '' && !is_numeric($prezzo_max)) {
        $prezzo_max = '';
    }

    if ($prezzo_min !== '' && $prezzo_max !== '' && (float) $prezzo_min > (float) $prezzo_max) {
        $tmp = $prezzo_min;
        $prezzo_min = $prezzo_max;
        $prezzo_max = $tmp;
    }

    return [
        'tipo' => $tipo,
        'prezzo_min' => $prezzo_min,
        'prezzo_max' => $prezzo_max,
    ];
}

/**
 * Applica la ricerca prodotti con filtri custom (tipologia e range prezzo).
 */
function ev_apply_product_search_filters($query) {
    if (is_admin() || !$query instanceof WP_Query || !$query->is_main_query() || !$query->is_search()) {
        return;
    }

    $request_post_type = isset($_GET['post_type']) ? sanitize_key(wp_unslash((string) $_GET['post_type'])) : '';

    // La barra di ricerca del tema lavora solo sui prodotti.
    if ($request_post_type !== '' && $request_post_type !== 'prodotto') {
        return;
    }

    $filters = ev_get_search_product_filters();
    $query->set('post_type', 'prodotto');
    $query->set('post_status', 'publish');
    $query->set('posts_per_page', 12);

    $tax_query = [];
    if ($filters['tipo'] !== '') {
        $tax_query[] = [
            'taxonomy' => 'categoria_prodotto',
            'field'    => 'slug',
            'terms'    => [$filters['tipo']],
        ];
    }

    if (!empty($tax_query)) {
        $query->set('tax_query', $tax_query);
    }

    $meta_query = [];
    if ($filters['prezzo_min'] !== '' || $filters['prezzo_max'] !== '') {
        $price_clause = [
            'key'     => '_dci_prodotto_prezzo',
            'type'    => 'DECIMAL(10,2)',
            'compare' => 'BETWEEN',
            'value'   => [0, 999999],
        ];

        if ($filters['prezzo_min'] !== '' && $filters['prezzo_max'] !== '') {
            $price_clause['value'] = [(float) $filters['prezzo_min'], (float) $filters['prezzo_max']];
        } elseif ($filters['prezzo_min'] !== '') {
            $price_clause['compare'] = '>=';
            $price_clause['value'] = (float) $filters['prezzo_min'];
        } else {
            $price_clause['compare'] = '<=';
            $price_clause['value'] = (float) $filters['prezzo_max'];
        }

        $meta_query[] = $price_clause;
    }

    if (!empty($meta_query)) {
        $query->set('meta_query', $meta_query);
    }
}
add_action('pre_get_posts', 'ev_apply_product_search_filters');

/**
 * Restituisce l'URL della pagina catalogo con tutti i prodotti.
 *
 * @return string
 */
function ev_get_all_products_page_url() {
    $all_products_page = get_page_by_path('tutti-prodotti');

    if ($all_products_page instanceof WP_Post) {
        return get_permalink($all_products_page);
    }

    return home_url('/tutti-prodotti/');
}

/**
 * Restituisce lo stato del prodotto in formato pronto per UI.
 * Priorita: non disponibile > esaurito > in arrivo > disponibile.
 *
 * @param int        $product_id ID prodotto.
 * @param int|string $stock_qty  Quantita disponibile (opzionale).
 * @return array{label:string,slug:string,class:string}
 */
function ev_get_product_availability_indicator($product_id, $stock_qty = '') {
    $status_candidates = [];
    $terms = get_the_terms((int) $product_id, 'stato_prodotto');

    if (!is_wp_error($terms) && !empty($terms)) {
        foreach ($terms as $term) {
            if ($term instanceof WP_Term) {
                $status_candidates[] = sanitize_title((string) $term->slug);
                $status_candidates[] = sanitize_title((string) $term->name);
            }
        }
    }

    $priority_map = [
        'non-disponibile' => ['label' => 'Non disponibile', 'class' => 'is-not-available'],
        'non_disponibile' => ['label' => 'Non disponibile', 'class' => 'is-not-available'],
        'esaurito'        => ['label' => 'Esaurito', 'class' => 'is-sold-out'],
        'in-arrivo'       => ['label' => 'In arrivo', 'class' => 'is-arriving'],
        'in_arrivo'       => ['label' => 'In arrivo', 'class' => 'is-arriving'],
        'disponibile'     => ['label' => 'Disponibile', 'class' => 'is-available'],
    ];

    foreach (['non-disponibile', 'non_disponibile', 'esaurito', 'in-arrivo', 'in_arrivo', 'disponibile'] as $status_key) {
        if (in_array($status_key, $status_candidates, true)) {
            return [
                'label' => $priority_map[$status_key]['label'],
                'slug'  => $status_key,
                'class' => $priority_map[$status_key]['class'],
            ];
        }
    }

    if ($stock_qty !== '' && is_numeric($stock_qty)) {
        if ((int) $stock_qty > 0) {
            return [
                'label' => 'Disponibile',
                'slug'  => 'disponibile',
                'class' => 'is-available',
            ];
        }

        return [
            'label' => 'Esaurito',
            'slug'  => 'esaurito',
            'class' => 'is-sold-out',
        ];
    }

    return [
        'label' => 'Non disponibile',
        'slug'  => 'non-disponibile',
        'class' => 'is-not-available',
    ];
}

/**
 * Garantisce che la pagina "Tutti i prodotti" esista nel sito.
 * Se manca, la crea e assegna il template dedicato.
 *
 * @return void
 */
function ev_ensure_all_products_page_exists() {
    $slug = 'tutti-prodotti';
    $page = get_page_by_path($slug);

    if (!$page instanceof WP_Post) {
        $page_id = wp_insert_post([
            'post_type'   => 'page',
            'post_title'  => 'Tutti i prodotti',
            'post_name'   => $slug,
            'post_status' => 'publish',
        ], true);

        if (!is_wp_error($page_id) && (int) $page_id > 0) {
            update_post_meta((int) $page_id, '_wp_page_template', 'page-template/tutti-prodotti.php');
        }
    } else {
        update_post_meta((int) $page->ID, '_wp_page_template', 'page-template/tutti-prodotti.php');
    }
}
add_action('init', 'ev_ensure_all_products_page_exists', 30);

/**
 * Garantisce la presenza delle pagine principali del tema e il collegamento al template corretto.
 *
 * @return void
 */
function ev_ensure_core_theme_pages_exist() {
    $required_pages = [
        [
            'title'    => 'Abbigliamento',
            'slug'     => 'abbigliamento',
            'template' => 'page-template/abbigliamento-page.php',
        ],
        [
            'title'    => 'Accessori',
            'slug'     => 'accessori',
            'template' => 'page-template/accessori.php',
        ],
        [
            'title'    => 'Offerte',
            'slug'     => 'offerte',
            'template' => 'page-template/offerte.php',
        ],
        [
            'title'    => 'Login',
            'slug'     => 'login',
            'template' => 'page-template/login.php',
        ],
    ];

    foreach ($required_pages as $page_data) {
        $template_path = get_template_directory() . '/' . $page_data['template'];
        if (!file_exists($template_path)) {
            continue;
        }

        $page = get_page_by_path($page_data['slug']);
        if (!$page instanceof WP_Post) {
            $page_id = wp_insert_post([
                'post_type'   => 'page',
                'post_title'  => $page_data['title'],
                'post_name'   => $page_data['slug'],
                'post_status' => 'publish',
            ], true);

            if (!is_wp_error($page_id) && (int) $page_id > 0) {
                update_post_meta((int) $page_id, '_wp_page_template', $page_data['template']);
            }
            continue;
        }

        update_post_meta((int) $page->ID, '_wp_page_template', $page_data['template']);
    }

}
add_action('init', 'ev_ensure_core_theme_pages_exist', 31);

/**
 * Estrae ricorsivamente ID post da una struttura mista (array/stringhe).
 *
 * @param mixed $value
 * @return array<int>
 */
function ev_extract_post_ids_from_mixed($value) {
    $ids = [];

    if (is_numeric($value)) {
        $id = (int) $value;
        if ($id > 0) {
            $ids[] = $id;
        }
        return $ids;
    }

    if (is_string($value)) {
        $parts = preg_split('/[,\s|;]+/', $value) ?: [];
        foreach ($parts as $part) {
            if (is_numeric($part)) {
                $id = (int) $part;
                if ($id > 0) {
                    $ids[] = $id;
                }
            }
        }
        return $ids;
    }

    if (is_array($value)) {
        foreach ($value as $item) {
            $ids = array_merge($ids, ev_extract_post_ids_from_mixed($item));
        }
    }

    return $ids;
}

/**
 * Restituisce gli ID dei prodotti evidenziati in Homepage in base a:
 * - tassonomia posizione_evidenziata = homepage
 * - stato prodotto diverso da esaurito/non disponibile
 * - finestra date attiva (se valorizzata)
 *
 * @return array<int>
 */
function ev_get_homepage_featured_product_ids() {
    $now = current_time('timestamp');

    $homepage_term = get_term_by('slug', 'homepage', 'posizione_evidenziata');
    if (!$homepage_term || is_wp_error($homepage_term)) {
        $homepage_term = get_term_by('name', 'Homepage', 'posizione_evidenziata');
    }

    if (!$homepage_term || is_wp_error($homepage_term)) {
        return [];
    }

    $excluded_state_ids = [];
    $state_terms = get_terms([
        'taxonomy'   => 'stato_prodotto',
        'hide_empty' => false,
    ]);

    if (!is_wp_error($state_terms) && !empty($state_terms)) {
        foreach ($state_terms as $state_term) {
            if (!$state_term instanceof WP_Term) {
                continue;
            }

            $state_slug = sanitize_title((string) $state_term->slug);
            $state_name = sanitize_title((string) $state_term->name);

            if (in_array($state_slug, ['esaurito', 'non-disponibile'], true) || in_array($state_name, ['esaurito', 'non-disponibile'], true)) {
                $excluded_state_ids[] = (int) $state_term->term_id;
            }
        }
    }

    $tax_query = [
        'relation' => 'AND',
        [
            'taxonomy' => 'posizione_evidenziata',
            'field'    => 'term_id',
            'terms'    => [(int) $homepage_term->term_id],
        ],
    ];

    if (!empty($excluded_state_ids)) {
        $tax_query[] = [
            'taxonomy' => 'stato_prodotto',
            'field'    => 'term_id',
            'terms'    => $excluded_state_ids,
            'operator' => 'NOT IN',
        ];
    }

    $meta_query = [
        'relation' => 'AND',
        [
            'relation' => 'OR',
            [
                'key'     => '_dci_prodotto_data_inizio_evidenza',
                'compare' => 'NOT EXISTS',
            ],
            [
                'key'     => '_dci_prodotto_data_inizio_evidenza',
                'value'   => '',
                'compare' => '=',
            ],
            [
                'key'     => '_dci_prodotto_data_inizio_evidenza',
                'value'   => $now,
                'compare' => '<=',
                'type'    => 'NUMERIC',
            ],
        ],
        [
            'relation' => 'OR',
            [
                'key'     => '_dci_prodotto_data_fine_evidenza',
                'compare' => 'NOT EXISTS',
            ],
            [
                'key'     => '_dci_prodotto_data_fine_evidenza',
                'value'   => '',
                'compare' => '=',
            ],
            [
                'key'     => '_dci_prodotto_data_fine_evidenza',
                'value'   => $now,
                'compare' => '>=',
                'type'    => 'NUMERIC',
            ],
        ],
    ];

    $featured_ids = get_posts([
        'post_type'      => 'prodotto',
        'post_status'    => 'publish',
        'tax_query'      => $tax_query,
        'meta_query'     => $meta_query,
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'fields'         => 'ids',
    ]);

    if (empty($featured_ids)) {
        return [];
    }

    return array_values(array_unique(array_map('intval', $featured_ids)));
}

/**
 * Restituisce gli ID degli stati prodotto da escludere dalle sezioni in evidenza.
 *
 * @return array<int>
 */
function ev_get_unavailable_state_term_ids() {
    $excluded_state_ids = [];
    $state_terms = get_terms([
        'taxonomy'   => 'stato_prodotto',
        'hide_empty' => false,
    ]);

    if (is_wp_error($state_terms) || empty($state_terms)) {
        return [];
    }

    foreach ($state_terms as $state_term) {
        if (!$state_term instanceof WP_Term) {
            continue;
        }

        $state_slug = sanitize_title((string) $state_term->slug);
        $state_name = sanitize_title((string) $state_term->name);

        if (
            in_array($state_slug, ['esaurito', 'non-disponibile', 'non_disponibile'], true) ||
            in_array($state_name, ['esaurito', 'non-disponibile', 'non_disponibile'], true)
        ) {
            $excluded_state_ids[] = (int) $state_term->term_id;
        }
    }

    return array_values(array_unique($excluded_state_ids));
}

/**
 * Restituisce gli ID prodotto evidenziati per una specifica posizione.
 *
 * @param string $position_slug Slug posizione evidenziata.
 * @param string $position_name Nome posizione evidenziata (fallback).
 * @param int    $limit         Numero massimo risultati (-1 tutti).
 * @return array<int>
 */
function ev_get_featured_product_ids_by_position($position_slug, $position_name = '', $limit = -1) {
    $now = current_time('timestamp');

    $position_term = get_term_by('slug', sanitize_title((string) $position_slug), 'posizione_evidenziata');
    if ((!$position_term || is_wp_error($position_term)) && $position_name !== '') {
        $position_term = get_term_by('name', sanitize_text_field((string) $position_name), 'posizione_evidenziata');
    }

    if (!$position_term || is_wp_error($position_term)) {
        return [];
    }

    $tax_query = [
        'relation' => 'AND',
        [
            'taxonomy' => 'posizione_evidenziata',
            'field'    => 'term_id',
            'terms'    => [(int) $position_term->term_id],
        ],
    ];

    $excluded_state_ids = ev_get_unavailable_state_term_ids();
    if (!empty($excluded_state_ids)) {
        $tax_query[] = [
            'taxonomy' => 'stato_prodotto',
            'field'    => 'term_id',
            'terms'    => $excluded_state_ids,
            'operator' => 'NOT IN',
        ];
    }

    $meta_query = [
        'relation' => 'AND',
        [
            'relation' => 'OR',
            [
                'key'     => '_dci_prodotto_data_inizio_evidenza',
                'compare' => 'NOT EXISTS',
            ],
            [
                'key'     => '_dci_prodotto_data_inizio_evidenza',
                'value'   => '',
                'compare' => '=',
            ],
            [
                'key'     => '_dci_prodotto_data_inizio_evidenza',
                'value'   => $now,
                'compare' => '<=',
                'type'    => 'NUMERIC',
            ],
        ],
        [
            'relation' => 'OR',
            [
                'key'     => '_dci_prodotto_data_fine_evidenza',
                'compare' => 'NOT EXISTS',
            ],
            [
                'key'     => '_dci_prodotto_data_fine_evidenza',
                'value'   => '',
                'compare' => '=',
            ],
            [
                'key'     => '_dci_prodotto_data_fine_evidenza',
                'value'   => $now,
                'compare' => '>=',
                'type'    => 'NUMERIC',
            ],
        ],
    ];

    $posts_per_page = (int) $limit;
    if ($posts_per_page === 0) {
        $posts_per_page = -1;
    }

    $featured_ids = get_posts([
        'post_type'      => 'prodotto',
        'post_status'    => 'publish',
        'tax_query'      => $tax_query,
        'meta_query'     => $meta_query,
        'posts_per_page' => $posts_per_page,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'fields'         => 'ids',
    ]);

    if (empty($featured_ids)) {
        return [];
    }

    return array_values(array_unique(array_map('intval', $featured_ids)));
}

/**
 * Recupera l'ID della categoria richiesta piu le sue sottocategorie.
 *
 * @param string $taxonomy Tassonomia.
 * @param string $slug     Slug termine.
 * @param string $name     Nome termine fallback.
 * @return array<int>
 */
function ev_get_term_ids_with_children($taxonomy, $slug, $name = '') {
    $term = get_term_by('slug', sanitize_title((string) $slug), $taxonomy);
    if ((!$term || is_wp_error($term)) && $name !== '') {
        $term = get_term_by('name', sanitize_text_field((string) $name), $taxonomy);
    }

    if (!$term || is_wp_error($term)) {
        return [];
    }

    $children = get_term_children((int) $term->term_id, $taxonomy);
    if (is_wp_error($children)) {
        $children = [];
    }

    $ids = array_merge([(int) $term->term_id], array_map('intval', $children));
    return array_values(array_unique($ids));
}
