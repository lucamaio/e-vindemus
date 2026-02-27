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