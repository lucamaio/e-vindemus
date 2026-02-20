<?php

require_once get_template_directory() . '/inc/activation.php';

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

    // Stile principale del tema (child-theme safe)
    wp_enqueue_style(
        'theme-style',
        get_stylesheet_uri(),
        ['bootstrap-css'],
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
