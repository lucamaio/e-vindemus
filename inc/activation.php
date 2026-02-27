<?php

/**
 * Esegue una ricarica completa dei componenti core del tema:
 * - tipologie e tassonomie
 * - termini custom
 * - rewrite rules
 * - opzioni di configurazione (opzionale reset default)
 *
 * @param bool $reset_options Se true, reimposta le opzioni principali ai valori di default.
 * @return array{templates_synced:bool,errors:array<int,string>,rolled_back:bool}
 */
function dci_reload_theme_components($reset_options = false) {
    set_time_limit(400);

    $report = [
        'templates_synced' => false,
        'errors'           => [],
        'rolled_back'      => false,
    ];

    // Inserisco i termini di tassonomia solo se la funzione esiste.
    if (function_exists('insertCustomTaxonomyTerms')) {
        insertCustomTaxonomyTerms();
    }

    $pages_sync_report = dci_create_pages_from_json_config();
    $report['templates_synced'] = !empty($pages_sync_report['success']);
    $report['errors'] = !empty($pages_sync_report['errors']) ? $pages_sync_report['errors'] : [];
    $report['rolled_back'] = !empty($pages_sync_report['rolled_back']);

    if ($reset_options) {
        update_option('ev_home_alert_options', [
            'message' => '🚚 Spedizione gratuita sopra i 59€ · Resi entro 30 giorni',
            'color'   => 'blue',
            'show'    => false,
        ]);
    }

    flush_rewrite_rules(false);

    return $report;
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
 * Crea/aggiorna pagine da configurazione JSON in modo idempotente.
 * In caso di errore prova a ripristinare lo stato precedente.
 *
 * @return array{success:bool,errors:array<int,string>,rolled_back:bool}
 */
function dci_create_pages_from_json_config() {
    $pages = dci_get_pages_config_from_json();

    if (empty($pages)) {
        return [
            'success'     => false,
            'errors'      => ['Nessuna pagina valida trovata nella configurazione JSON.'],
            'rolled_back' => false,
        ];
    }

    $modified_pages = [];
    $created_page_ids = [];

    foreach ($pages as $page_data) {
        $existing_page = dci_get_page_by_slug_including_trashed($page_data['slug']);

        if ($existing_page instanceof WP_Post) {
            $modified_pages[$existing_page->ID] = dci_get_page_state_snapshot($existing_page->ID);

            if ($existing_page->post_status === 'trash') {
                $untrashed = wp_untrash_post($existing_page->ID);
                if (!$untrashed) {
                    dci_rollback_page_sync_changes($modified_pages, $created_page_ids);
                    return [
                        'success'     => false,
                        'errors'      => ["Impossibile ripristinare la pagina cestinata '{$page_data['slug']}'."],
                        'rolled_back' => true,
                    ];
                }
            }

            $updated_page = wp_update_post([
                'ID'         => $existing_page->ID,
                'post_title' => $page_data['name'],
                'post_name'  => $page_data['slug'],
            ], true);

            if (is_wp_error($updated_page)) {
                dci_rollback_page_sync_changes($modified_pages, $created_page_ids);
                return [
                    'success'     => false,
                    'errors'      => [$updated_page->get_error_message()],
                    'rolled_back' => true,
                ];
            }

            update_post_meta($existing_page->ID, '_wp_page_template', $page_data['template']);
            continue;
        }

        $page_id = wp_insert_post([
            'post_type'   => 'page',
            'post_title'  => $page_data['name'],
            'post_name'   => $page_data['slug'],
            'post_status' => 'publish',
        ], true);

        if (is_wp_error($page_id) || $page_id <= 0) {
            dci_rollback_page_sync_changes($modified_pages, $created_page_ids);
            $error_message = is_wp_error($page_id)
                ? $page_id->get_error_message()
                : "Impossibile creare la pagina '{$page_data['slug']}'.";

            return [
                'success'     => false,
                'errors'      => [$error_message],
                'rolled_back' => true,
            ];
        }

        $created_page_ids[] = (int) $page_id;
        update_post_meta($page_id, '_wp_page_template', $page_data['template']);
    }

    return [
        'success'     => true,
        'errors'      => [],
        'rolled_back' => false,
    ];
}

/**
 * Salva uno snapshot dello stato pagina prima della sincronizzazione.
 *
 * @param int $page_id ID pagina.
 * @return array<string,mixed>
 */
function dci_get_page_state_snapshot($page_id) {
    $post = get_post($page_id);

    if (!$post instanceof WP_Post) {
        return [];
    }

    return [
        'ID'       => $post->ID,
        'title'    => $post->post_title,
        'slug'     => $post->post_name,
        'status'   => $post->post_status,
        'template' => (string) get_post_meta($post->ID, '_wp_page_template', true),
    ];
}

/**
 * Effettua rollback compensativo su pagine modificate/create durante la sync.
 *
 * @param array<int,array<string,mixed>> $modified_pages Snapshot pagine esistenti.
 * @param array<int,int>                 $created_page_ids IDs pagine create durante sync.
 * @return void
 */
function dci_rollback_page_sync_changes($modified_pages, $created_page_ids) {
    foreach ($created_page_ids as $created_page_id) {
        wp_delete_post((int) $created_page_id, true);
    }

    foreach ($modified_pages as $snapshot) {
        if (empty($snapshot['ID'])) {
            continue;
        }

        $post_id = (int) $snapshot['ID'];

        wp_update_post([
            'ID'         => $post_id,
            'post_title' => isset($snapshot['title']) ? $snapshot['title'] : '',
            'post_name'  => isset($snapshot['slug']) ? $snapshot['slug'] : '',
            'post_status'=> 'publish',
        ]);

        if (isset($snapshot['template']) && $snapshot['template'] !== '') {
            update_post_meta($post_id, '_wp_page_template', $snapshot['template']);
        } else {
            delete_post_meta($post_id, '_wp_page_template');
        }

        if (isset($snapshot['status']) && $snapshot['status'] === 'trash') {
            wp_trash_post($post_id);
        }
    }

    return true;
}

/**
 * Recupera una pagina per slug anche se cestinata.
 *
 * @param string $slug Slug della pagina.
 * @return WP_Post|null
 */
function dci_get_page_by_slug_including_trashed($slug) {
    $pages = get_posts([
        'post_type'      => 'page',
        'name'           => $slug,
        'post_status'    => 'any',
        'posts_per_page' => 1,
    ]);

    return isset($pages[0]) && $pages[0] instanceof WP_Post ? $pages[0] : null;
}

/**
 * Recupera una pagina per slug anche se cestinata.
 *
 * @param string $slug Slug della pagina.
 * @return WP_Post|null
 */
function dci_get_page_by_slug_including_trashed($slug) {
    $pages = get_posts([
        'post_type'      => 'page',
        'name'           => $slug,
        'post_status'    => 'any',
        'posts_per_page' => 1,
    ]);

    return isset($pages[0]) && $pages[0] instanceof WP_Post ? $pages[0] : null;
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
    $error_message = isset($_GET['dci_reload_error']) ? sanitize_text_field(wp_unslash($_GET['dci_reload_error'])) : '';
    ?>
    <div class="wrap">
        <h1>Ricarica configurazione tema</h1>

        <?php if ($status === 'ok') : ?>
            <div class="notice notice-success is-dismissible"><p>Ricarica completata con successo.</p></div>
        <?php elseif ($status === 'ko') : ?>
            <div class="notice notice-error is-dismissible">
                <p>Si è verificato un errore durante la ricarica. Le modifiche ai template sono state annullate.</p>
                <?php if ($error_message !== '') : ?>
                    <p><strong>Dettaglio:</strong> <?php echo esc_html($error_message); ?></p>
                <?php endif; ?>
            </div>
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

    $report = dci_reload_theme_components($reset_options);

    $status = !empty($report['templates_synced']) ? 'ok' : 'ko';

    $query_args = [
        'page'       => 'dci-theme-reload',
        'dci_reload' => $status,
    ];

    if (!empty($report['errors'][0])) {
        $query_args['dci_reload_error'] = $report['errors'][0];
    }

    $redirect_url = add_query_arg($query_args, admin_url('themes.php'));

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
            $existing_parent = term_exists($key, $tax_name);

            if ($existing_parent) {
                $existing_parent_id = is_array($existing_parent) ? (int) $existing_parent['term_id'] : (int) $existing_parent;
                recursionInsertTaxonomy($value, $tax_name, $existing_parent_id);
                continue;
            }

            $parent = $parent_id !== null ? wp_insert_term($key, $tax_name, ['parent' => $parent_id]) : wp_insert_term($key, $tax_name);
            if (is_wp_error($parent)) {
                continue;
            }

            if (is_array($parent) && isset($parent['term_id'])) {
                recursionInsertTaxonomy($value, $tax_name, (int) $parent['term_id']);
            }
        } else {
            if (term_exists($value, $tax_name)) {
                continue;
            }

            $parent_id !== null ? wp_insert_term($value, $tax_name, ['parent' => $parent_id]) : wp_insert_term($value, $tax_name);
        }
    }
}
