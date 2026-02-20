<?php

/**
 * Attivazione del tema.
 */
function dci_theme_activation() {
    set_time_limit(400);

    // Inserisco i termini di tassonomia solo se la funzione esiste.
    if (function_exists('insertCustomTaxonomyTerms')) {
        insertCustomTaxonomyTerms();
    }

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

    // Qui possiamo inserire setup iniziali (rewrite, opzioni default, ecc).
    flush_rewrite_rules(false);

    update_option('dci_theme_just_activated', 0);
}
add_action('init', 'dci_theme_post_activation_bootstrap');
