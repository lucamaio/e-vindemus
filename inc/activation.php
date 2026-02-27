<?php

/**
 * Esegue una ricarica completa dei componenti core del tema:
 * - tipologie e tassonomie
 * - termini custom
 * - rewrite rules
 * - opzioni di configurazione (opzionale reset default)
 *
 * @param bool $reset_options Se true, reimposta le opzioni principali ai valori di default.
 */
function dci_reload_theme_components($reset_options = false) {
    set_time_limit(400);

    // Inserisco i termini di tassonomia solo se la funzione esiste.
    if (function_exists('insertCustomTaxonomyTerms')) {
        insertCustomTaxonomyTerms();
    }

    dci_create_pages_from_json_config();

    if ($reset_options) {
        update_option('ev_home_alert_options', [
            'message' => '🚚 Spedizione gratuita sopra i 59€ · Resi entro 30 giorni',
            'color'   => 'blue',
            'show'    => false,
        ]);
    }

    flush_rewrite_rules(false);
}

/**
 * Legge e valida la configurazione delle pagine da file JSON.
 *
 * @return array<int, array{name:string,slug:string,template:string}>
 */
function dci_get_pages_config_from_json() {
    $json_file_path = get_template_directory() . '/inc/page.json';

    if (!file_exists($json_file_path) || !is_readable($json_file_path)) {
        return [];
    }

    $json_content = file_get_contents($json_file_path);

    if ($json_content === false) {
        return [];
    }

    $pages = json_decode($json_content, true);

    if (!is_array($pages)) {
        return [];
    }

    $valid_pages = [];

    foreach ($pages as $page_data) {
        if (!is_array($page_data)) {
            continue;
        }

        $name     = isset($page_data['name']) ? sanitize_text_field($page_data['name']) : '';
        $slug     = isset($page_data['slug']) ? sanitize_title($page_data['slug']) : '';
        $template = isset($page_data['template']) ? sanitize_text_field($page_data['template']) : '';

        if ($name === '' || $slug === '' || $template === '') {
            continue;
        }

        $template_file = get_template_directory() . '/' . ltrim($template, '/');
        if (!file_exists($template_file)) {
            continue;
        }

        $valid_pages[] = [
            'name'     => $name,
            'slug'     => $slug,
            'template' => ltrim($template, '/'),
        ];
    }

    return $valid_pages;
}

/**
 * Crea pagine da configurazione JSON una sola volta (se non già presenti).
 */
function dci_create_pages_from_json_config() {
    $pages = dci_get_pages_config_from_json();

    if (empty($pages)) {
        return;
    }

    foreach ($pages as $page_data) {
        $existing_page = get_page_by_path($page_data['slug'], OBJECT, 'page');

        if ($existing_page instanceof WP_Post) {
            continue;
        }

        $page_id = wp_insert_post([
            'post_type'   => 'page',
            'post_title'  => $page_data['name'],
            'post_name'   => $page_data['slug'],
            'post_status' => 'publish',
        ]);

        if (!is_wp_error($page_id) && $page_id > 0) {
            update_post_meta($page_id, '_wp_page_template', $page_data['template']);
        }
    }
}

/**
 * Attivazione del tema.
 */
function dci_theme_activation() {
    dci_reload_theme_components();

    // inserisco i termini di tassonomia
    insertCustomTaxonomyTerms();

    // Aggiorno le descrizioni delle tassonomie per eventuali modifiche
    updateCategorieDescription();

    // Flag utile per eventuali bootstrap post-attivazione.
    update_option('dci_theme_just_activated', 1);
}
add_action('after_switch_theme', 'dci_theme_activation');

/**
 * Bootstrap post-attivazione: esegue una sola volta setup iniziale.
 */
function dci_theme_post_activation_bootstrap() {
    if ((int) get_option('dci_theme_just_activated', 0) !== 1) {
        return;
    }

    dci_reload_theme_components();

    update_option('dci_theme_just_activated', 0);
}
add_action('init', 'dci_theme_post_activation_bootstrap', 99);

/**
 * Pagina admin per ricarica manuale configurazione tema.
 */
function dci_register_theme_reload_page() {
    add_theme_page(
        'Ricarica configurazione tema',
        'Ricarica configurazione',
        'manage_options',
        'dci-theme-reload',
        'dci_render_theme_reload_page'
    );
}
add_action('admin_menu', 'dci_register_theme_reload_page');

function dci_render_theme_reload_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $status = isset($_GET['dci_reload']) ? sanitize_text_field(wp_unslash($_GET['dci_reload'])) : '';
    ?>
    <div class="wrap">
        <h1>Ricarica configurazione tema</h1>

        <?php if ($status === 'ok') : ?>
            <div class="notice notice-success is-dismissible"><p>Ricarica completata con successo.</p></div>
        <?php elseif ($status === 'ko') : ?>
            <div class="notice notice-error is-dismissible"><p>Si è verificato un errore durante la ricarica.</p></div>
        <?php endif; ?>

        <p>Usa questa utility per ricaricare template, tipologie, tassonomie e opzioni principali del tema.</p>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="dci_reload_theme_components">
            <?php wp_nonce_field('dci_reload_theme_components_action', 'dci_reload_theme_components_nonce'); ?>

            <p>
                <label>
                    <input type="checkbox" name="dci_reset_options" value="1">
                    Reimposta anche le opzioni tema ai valori di default
                </label>
            </p>

            <?php submit_button('Avvia ricarica'); ?>
        </form>
    </div>
    <?php
}

/**
 * Handler della ricarica manuale da admin.
 */
function dci_handle_theme_reload_request() {
    if (!current_user_can('manage_options')) {
        wp_die('Non autorizzato.');
    }

    check_admin_referer('dci_reload_theme_components_action', 'dci_reload_theme_components_nonce');

    $reset_options = isset($_POST['dci_reset_options']) && (int) $_POST['dci_reset_options'] === 1;

    dci_reload_theme_components($reset_options);

    $redirect_url = add_query_arg(
        ['page' => 'dci-theme-reload', 'dci_reload' => 'ok'],
        admin_url('themes.php')
    );

    wp_safe_redirect($redirect_url);
    exit;
}
add_action('admin_post_dci_reload_theme_components', 'dci_handle_theme_reload_request');


/**
 * Funzione responsabile dell'inserimento dei termini di tassonomia personalizzati.
 */

function insertCustomTaxonomyTerms() {
   /**
    * Categorie prodotti
    */

   $categorie_array = dci_categorie_prodotti_array();
   recursionInsertTaxonomy($categorie_array, 'categoria_prodotto');

}

/**
 * inserimento ricorsivo dei termini di tassonomia
 * @param $array
 * @param $tax_name
 * @param null $parent_id
 */
function recursionInsertTaxonomy($array, $tax_name, $parent_id = null) {
    foreach ($array as $key => $value) {
        if (!is_numeric($key)) { //se NON è numerico, ha dei figli
            if (!term_exists( $key , $tax_name)) {
                $parent = $parent_id !== null ? wp_insert_term( $key, $tax_name, array("parent" => $parent_id)) : wp_insert_term( $key, $tax_name );
                if(is_array($parent)){
                    recursionInsertTaxonomy($value, $tax_name, $parent['term_taxonomy_id']);
                }
            } else {
                //se il padre esiste già ma il figlio no (get id del padre in base al termine...)
            }
        } else {
            $parent_id !== null ? wp_insert_term( $value, $tax_name, array("parent" => $parent_id)) : wp_insert_term( $value, $tax_name);
        }
    }
}
