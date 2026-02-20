<!-- header.php -->
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php $ev_alert_options = ev_get_home_alert_options(); ?>
<header class="ev-header" role="banner">
  <div class="ev-header__topbar ev-header__topbar--<?php echo esc_attr($ev_alert_options['color']); ?>">
    <p><?php echo esc_html($ev_alert_options['message']); ?></p>
    <a href="#">Supporto clienti</a>
  </div>

  <div class="ev-header__main">
    <a class="ev-header__brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="Torna alla homepage">
      <span class="ev-header__logo-dot" aria-hidden="true"></span>
      <span>
        <?php bloginfo('name'); ?>
        <small><?php bloginfo('description'); ?></small>
      </span>
    </a>

    <form class="ev-header__search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
      <label class="screen-reader-text" for="ev-search-input">Cerca prodotti</label>
      <input id="ev-search-input" type="search" name="s" placeholder="Cerca brand, categorie o prodotti..." value="<?php echo get_search_query(); ?>">
      <button type="submit">Cerca</button>
    </form>

    <nav class="ev-header__actions" aria-label="Azioni utente">
      <a href="#" aria-label="Wishlist">♡ Wishlist</a>
      <a href="#" aria-label="Account">👤 Account</a>
      <a href="#" aria-label="Carrello">🛒 Carrello</a>
    </nav>
  </div>

  <div class="ev-header__nav-wrap">
    <nav class="ev-header__nav" aria-label="Categorie prodotto">
      <?php
      wp_nav_menu([
        'theme_location' => 'primary',
        'container'      => false,
        'menu_class'     => 'ev-header__menu',
        'fallback_cb'    => static function () {
          echo '<ul class="ev-header__menu">';
          echo '<li><a href="#">Novità</a></li>';
          echo '<li><a href="#">Donna</a></li>';
          echo '<li><a href="#">Uomo</a></li>';
          echo '<li><a href="#">Accessori</a></li>';
          echo '<li><a href="#">Offerte</a></li>';
          echo '</ul>';
        },
      ]);
      ?>
    </nav>
    <a class="ev-header__promo" href="#">🔥 Flash Deals fino al -40%</a>
  </div>
</header>
