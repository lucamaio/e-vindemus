<!-- header.php -->
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script>
  (function () {
    try {
      var savedTheme = window.localStorage.getItem('ev-theme-preference');
      var root = document.documentElement;
      if (savedTheme === 'dark') {
        root.classList.add('ev-theme-dark');
        root.setAttribute('data-theme', 'dark');
      } else if (savedTheme === 'light') {
        root.classList.add('ev-theme-light');
        root.setAttribute('data-theme', 'light');
      } else {
        root.removeAttribute('data-theme');
      }
    } catch (error) {}
  })();
</script>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php $ev_alert_options = ev_get_home_alert_options(); ?>
<?php
$ev_get_page_url = static function ($slug, $fallback = '#') {
    $page = get_page_by_path(sanitize_title((string) $slug));

    if ($page instanceof WP_Post) {
        return get_permalink($page->ID);
    }

    return $fallback;
};

$ev_mobile_menu_links = [
    'Pokemon' => $ev_get_page_url('pokemon', home_url('/pokemon/')),
    'Abbigliamento' => $ev_get_page_url('abbigliamento', home_url('/abbigliamento/')),
    'Accessori' => $ev_get_page_url('accessori', home_url('/accessori/')),
    'Offerte' => $ev_get_page_url('offerte', home_url('/offerte/')),
    'Account' => $ev_get_page_url('login', wp_login_url()),
    'Carrello' => $ev_get_page_url('carrello', home_url('/carrello/')),
];

$ev_mobile_menu_icons = [
    'Pokemon' => 'fa-solid fa-dragon',
    'Abbigliamento' => 'fa-solid fa-shirt',
    'Accessori' => 'fa-solid fa-gem',
    'Offerte' => 'fa-solid fa-tags',
    'Account' => 'fa-regular fa-user',
    'Carrello' => 'fa-solid fa-cart-shopping',
];

$ev_mobile_menu_shop_items = ['Pokemon', 'Abbigliamento', 'Accessori', 'Offerte'];
$ev_mobile_menu_account_items = ['Account', 'Carrello'];

$ev_search_filters = function_exists('ev_get_search_product_filters')
    ? ev_get_search_product_filters()
    : [
        'tipo' => isset($_GET['tipo']) ? sanitize_title(wp_unslash((string) $_GET['tipo'])) : '',
        'prezzo_min' => isset($_GET['prezzo_min']) ? sanitize_text_field(wp_unslash((string) $_GET['prezzo_min'])) : '',
        'prezzo_max' => isset($_GET['prezzo_max']) ? sanitize_text_field(wp_unslash((string) $_GET['prezzo_max'])) : '',
    ];

$ev_selected_type = $ev_search_filters['tipo'];
$ev_price_min = $ev_search_filters['prezzo_min'];
$ev_price_max = $ev_search_filters['prezzo_max'];
$ev_has_active_search_filters = ($ev_selected_type !== '' || $ev_price_min !== '' || $ev_price_max !== '');

$ev_type_terms = get_terms([
    'taxonomy'   => 'categoria_prodotto',
    'hide_empty' => false,
    'orderby'    => 'name',
    'order'      => 'ASC',
]);

if (is_wp_error($ev_type_terms)) {
    $ev_type_terms = [];
}
?>
<header class="ev-header" role="banner">
  <?php if ($ev_alert_options['show'] && !empty($ev_alert_options['message'])) : ?>
  <div class="ev-header__topbar ev-header__topbar--<?php echo esc_attr($ev_alert_options['color']); ?>">
    <p><?php echo esc_html($ev_alert_options['message']); ?></p>
    <a href="#">Supporto clienti</a>
  </div>
  <?php endif; ?>

  <div class="ev-header__main">
    <button
      type="button"
      class="ev-header__menu-toggle"
      aria-controls="ev-mobile-drawer"
      aria-expanded="false"
      aria-label="Apri menu principale"
      data-ev-menu-toggle>
      <span></span>
      <span></span>
      <span></span>
    </button>

    <a class="ev-header__brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="Torna alla homepage">
      <span class="ev-header__logo-dot" aria-hidden="true"></span>
      <span>
        <?php bloginfo('name'); ?>
        <small><?php bloginfo('description'); ?></small>
      </span>
    </a>

    <form class="ev-header__search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
      <input type="hidden" name="post_type" value="prodotto">
      <label class="screen-reader-text" for="ev-search-input">Cerca prodotti</label>
      <div class="ev-header__search-main">
        <span class="ev-header__search-icon" aria-hidden="true"></span>
        <input id="ev-search-input" type="search" name="s" placeholder="Cerca brand, categorie o prodotti..." value="<?php echo get_search_query(); ?>">
        <button
          type="button"
          class="ev-header__search-filter-toggle"
          aria-controls="ev-search-filters"
          aria-expanded="<?php echo $ev_has_active_search_filters ? 'true' : 'false'; ?>"
          data-ev-filters-toggle>
          <span class="ev-btn-icon ev-btn-icon--filter" aria-hidden="true"></span>
          Filtri
        </button>
        <button type="submit" class="ev-header__search-submit">
          <span class="ev-btn-icon ev-btn-icon--search" aria-hidden="true"></span>
          Cerca
        </button>
      </div>

      <div
        id="ev-search-filters"
        class="ev-header__search-filters"
        data-ev-filters-panel
        <?php echo $ev_has_active_search_filters ? '' : 'hidden'; ?>>
        <label class="screen-reader-text" for="ev-search-tipo">Filtra per tipologia</label>
        <select id="ev-search-tipo" name="tipo">
          <option value="">Tutte le tipologie</option>
          <?php foreach ($ev_type_terms as $term) : ?>
            <option value="<?php echo esc_attr($term->slug); ?>" <?php selected($ev_selected_type, $term->slug); ?>>
              <?php echo esc_html($term->name); ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label class="screen-reader-text" for="ev-search-price-min">Prezzo minimo</label>
        <input
          id="ev-search-price-min"
          type="number"
          name="prezzo_min"
          min="0"
          step="0.01"
          placeholder="Prezzo min"
          value="<?php echo esc_attr($ev_price_min); ?>">

        <label class="screen-reader-text" for="ev-search-price-max">Prezzo massimo</label>
        <input
          id="ev-search-price-max"
          type="number"
          name="prezzo_max"
          min="0"
          step="0.01"
          placeholder="Prezzo max"
          value="<?php echo esc_attr($ev_price_max); ?>">

        <div class="ev-header__search-filters-actions">
          <button type="submit" class="ev-header__search-submit ev-header__search-submit--secondary">
            <span class="ev-btn-icon ev-btn-icon--filter" aria-hidden="true"></span>
            Applica filtri
          </button>
        </div>
      </div>
    </form>

    <nav class="ev-header__actions" aria-label="Azioni utente">
      <a href="<?php echo esc_url($ev_mobile_menu_links['Account']); ?>" aria-label="Account">
        <i class="fa-regular fa-user" aria-hidden="true"></i>
        Account
      </a>
      <a href="<?php echo esc_url($ev_mobile_menu_links['Carrello']); ?>" aria-label="Carrello">
        <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
        Carrello
      </a>
    </nav>
  </div>

  <div class="ev-header__nav-wrap">
    <nav class="ev-header__nav" aria-label="Categorie prodotto">
      <?php
      wp_nav_menu([
        'theme_location' => 'primary',
        'container'      => false,
        'menu_class'     => 'ev-header__menu',
        'fallback_cb'    => static function () use ($ev_get_page_url) {
          echo '<ul class="ev-header__menu">';
          $pokemon_url = $ev_get_page_url('pokemon', home_url('/pokemon/'));
          $abbigliamento_url = $ev_get_page_url('abbigliamento', home_url('/abbigliamento/'));
          $accessori_url = $ev_get_page_url('accessori', home_url('/accessori/'));
          $offerte_url = $ev_get_page_url('offerte', home_url('/offerte/'));

          echo '<li><a href="' . esc_url($pokemon_url) . '">Pokemon</a></li>';
          echo '<li><a href="' . esc_url($abbigliamento_url) . '">Abbigliamento</a></li>';
          echo '<li><a href="' . esc_url($accessori_url) . '">Accessori</a></li>';
          echo '<li><a href="' . esc_url($offerte_url) . '">Offerte</a></li>';
          echo '</ul>';
        },
      ]);
      ?>
    </nav>
    <div class="ev-header__theme-wrap" aria-label="Impostazione tema">
      <button type="button" class="ev-theme-toggle ev-theme-toggle--header" data-ev-theme-toggle aria-label="Cambia tema">
        <span class="ev-theme-switch" aria-hidden="true">
          <span class="ev-theme-switch__thumb"></span>
        </span>
        <span class="ev-theme-switch__label" data-ev-theme-label>Tema</span>
      </button>
    </div>
    <!-- <a class="ev-header__promo" href="#">Flash Deals fino al -40%</a> -->
  </div>

  <div class="ev-mobile-nav-backdrop" data-ev-menu-close></div>
  <aside id="ev-mobile-drawer" class="ev-mobile-nav" aria-label="Menu mobile" aria-hidden="true">
    <div class="ev-mobile-nav__head">
      <strong>Menu</strong>
      <button type="button" class="ev-mobile-nav__close" aria-label="Chiudi menu" data-ev-menu-close>Chiudi</button>
    </div>
    <nav aria-label="Navigazione mobile principale">
      <ul class="ev-mobile-nav__list">
        <?php foreach ($ev_mobile_menu_shop_items as $label) : ?>
          <?php
          if (!isset($ev_mobile_menu_links[$label])) {
            continue;
          }
          $url = $ev_mobile_menu_links[$label];
          $icon_class = isset($ev_mobile_menu_icons[$label]) ? $ev_mobile_menu_icons[$label] : 'fa-solid fa-circle';
          ?>
          <li>
            <a href="<?php echo esc_url($url); ?>">
              <i class="<?php echo esc_attr($icon_class); ?>" aria-hidden="true"></i>
              <?php echo esc_html($label); ?>
            </a>
          </li>
        <?php endforeach; ?>

        <li class="ev-mobile-nav__divider" role="presentation" aria-hidden="true"></li>

        <li>
          <button type="button" class="ev-mobile-nav__theme-toggle ev-theme-toggle" data-ev-theme-toggle aria-label="Cambia tema">
            <span class="ev-theme-switch" aria-hidden="true">
              <span class="ev-theme-switch__thumb"></span>
            </span>
            <span class="ev-theme-switch__label" data-ev-theme-label>Tema</span>
          </button>
        </li>

        <?php foreach ($ev_mobile_menu_account_items as $label) : ?>
          <?php
          if (!isset($ev_mobile_menu_links[$label])) {
            continue;
          }
          $url = $ev_mobile_menu_links[$label];
          $icon_class = isset($ev_mobile_menu_icons[$label]) ? $ev_mobile_menu_icons[$label] : 'fa-solid fa-circle';
          ?>
          <li>
            <a href="<?php echo esc_url($url); ?>">
              <i class="<?php echo esc_attr($icon_class); ?>" aria-hidden="true"></i>
              <?php echo esc_html($label); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>
  </aside>
</header>

<script>
  (function () {
    var body = document.body;
    var root = document.documentElement;
    var drawer = document.getElementById('ev-mobile-drawer');
    var toggle = document.querySelector('[data-ev-menu-toggle]');
    var closers = document.querySelectorAll('[data-ev-menu-close]');
    var themeToggles = document.querySelectorAll('[data-ev-theme-toggle]');
    var themeLabels = document.querySelectorAll('[data-ev-theme-label]');
    var themeStorageKey = 'ev-theme-preference';

    var getSystemTheme = function () {
      return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    };

    var getManualTheme = function () {
      try {
        var saved = window.localStorage.getItem(themeStorageKey);
        return (saved === 'dark' || saved === 'light') ? saved : '';
      } catch (error) {
        return '';
      }
    };

    var getActiveTheme = function () {
      var manual = getManualTheme();
      return manual || getSystemTheme();
    };

    var setTheme = function (theme) {
      root.classList.remove('ev-theme-dark', 'ev-theme-light');
      root.removeAttribute('data-theme');

      if (theme === 'dark') {
        root.classList.add('ev-theme-dark');
        root.setAttribute('data-theme', 'dark');
      } else if (theme === 'light') {
        root.classList.add('ev-theme-light');
        root.setAttribute('data-theme', 'light');
      }
    };

    var updateThemeButtons = function () {
      var active = getActiveTheme();
      var isDark = active === 'dark';

      themeLabels.forEach(function (label) {
        label.textContent = isDark ? 'Scuro' : 'Chiaro';
      });

      themeToggles.forEach(function (btn) {
        btn.setAttribute('data-theme-state', isDark ? 'dark' : 'light');
        btn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
      });
    };

    var toggleTheme = function () {
      var active = getActiveTheme();
      var next = (active === 'dark') ? 'light' : 'dark';
      try {
        window.localStorage.setItem(themeStorageKey, next);
      } catch (error) {}
      setTheme(next);
      updateThemeButtons();
    };

    setTheme(getManualTheme());
    updateThemeButtons();

    themeToggles.forEach(function (btn) {
      btn.addEventListener('click', function () {
        toggleTheme();
      });
    });

    var mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    if (mediaQuery && typeof mediaQuery.addEventListener === 'function') {
      mediaQuery.addEventListener('change', function () {
        if (!getManualTheme()) {
          updateThemeButtons();
        }
      });
    }

    if (!drawer || !toggle) {
      return;
    }

    var setMenuState = function (isOpen) {
      body.classList.toggle('ev-menu-open', isOpen);
      drawer.classList.toggle('is-open', isOpen);
      drawer.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    };

    var searchForm = document.querySelector('.ev-header__search');
    var filtersToggle = searchForm ? searchForm.querySelector('[data-ev-filters-toggle]') : null;
    var filtersPanel = searchForm ? searchForm.querySelector('[data-ev-filters-panel]') : null;

    var setFiltersState = function (isOpen) {
      if (!filtersToggle || !filtersPanel) {
        return;
      }

      filtersToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

      if (isOpen) {
        filtersPanel.removeAttribute('hidden');
      } else {
        filtersPanel.setAttribute('hidden', 'hidden');
      }
    };

    toggle.addEventListener('click', function () {
      var isOpen = drawer.classList.contains('is-open');
      setMenuState(!isOpen);
    });

    if (filtersToggle && filtersPanel) {
      filtersToggle.addEventListener('click', function () {
        var isOpen = !filtersPanel.hasAttribute('hidden');
        setFiltersState(!isOpen);
      });

      document.addEventListener('click', function (event) {
        if (!searchForm.contains(event.target)) {
          setFiltersState(false);
        }
      });
    }

    closers.forEach(function (closer) {
      closer.addEventListener('click', function () {
        setMenuState(false);
      });
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        setMenuState(false);
        setFiltersState(false);
      }
    });
  })();
</script>
