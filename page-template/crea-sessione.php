<?php
/**
 * Template Name: Pagina Inizializza Sessione
 * Template Post Type: page
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (isset($_SESSION['user_id']) || isset($_SESSION['token'])) {
    wp_safe_redirect(home_url());
    exit;
}

if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    wp_safe_redirect(home_url('/login'));
    exit;
}

$user_id = isset($_POST['user_id']) ? absint(wp_unslash($_POST['user_id'])) : 0;
$token = isset($_POST['token']) ? sanitize_text_field(wp_unslash($_POST['token'])) : '';

if (empty($user_id) || empty($token)) {
    wp_safe_redirect(home_url('/login'));
    exit;
}

$_SESSION['user_id'] = $user_id;
$_SESSION['token'] = $token;

wp_safe_redirect(home_url());
exit;
