<?php

/**
 * attivazione del Tema
 */
function dci_theme_activation() {

    set_time_limit(400);  // Imposta il limite a 400 secondi per questa funzione
    
    // inserisco i termini di tassonomia
    insertCustomTaxonomyTerms();
}

add_action( 'after_switch_theme', 'dci_theme_activation' );