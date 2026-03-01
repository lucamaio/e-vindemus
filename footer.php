<!-- <div id="backToTop" data-bs-toggle="backtotop" class="back-to-top back-to-top-show back-to-top-show" style="overflow:hidden; cursor:pointer; box-shadow:0 4px 8px rgba(0,0,0,0.2); background-color:white; transition: background-color 0.3s;">
  <svg class="icon">
    <use href="#it-collapse"></use>
  </svg>
</div> -->

<?php
$ev_footer_page_url = static function ($slug, $fallback = '#') {
    $page = get_page_by_path($slug);

    if ($page instanceof WP_Post) {
        return get_permalink($page->ID);
    }

    return $fallback;
};
?>
<footer class="footer-app mt-auto">
    <div class="container">
        <div class="footer-app__grid">
            <section class="footer-app__brand">
                <h5>E-vindemus</h5>
                <p>Il tuo e-commerce per collezionabili, abbigliamento e accessori. Spedizioni veloci e supporto dedicato.</p>
                <a class="footer-app__cta" href="<?php echo esc_url($ev_footer_page_url('offerte')); ?>">
                    <i class="fa-solid fa-bolt" aria-hidden="true"></i>
                    Scopri le offerte
                </a>
            </section>

            <nav class="footer-app__col" aria-label="Navigazione shop">
                <h6>Shop</h6>
                <ul>
                    <li><a class="footer-link" href="<?php echo esc_url($ev_footer_page_url('pokemon')); ?>">Pokemon</a></li>
                    <li><a class="footer-link" href="<?php echo esc_url($ev_footer_page_url('abbigliamento')); ?>">Abbigliamento</a></li>
                    <li><a class="footer-link" href="<?php echo esc_url($ev_footer_page_url('accessori')); ?>">Accessori</a></li>
                    <li><a class="footer-link" href="<?php echo esc_url($ev_footer_page_url('offerte')); ?>">Offerte</a></li>
                </ul>
            </nav>

            <nav class="footer-app__col" aria-label="Navigazione assistenza">
                <h6>Assistenza</h6>
                <ul>
                    <li><a class="footer-link" href="<?php echo esc_url($ev_footer_page_url('contatti')); ?>">Contatti</a></li>
                    <li><a class="footer-link" href="<?php echo esc_url($ev_footer_page_url('spedizioni')); ?>">Spedizioni</a></li>
                    <li><a class="footer-link" href="<?php echo esc_url($ev_footer_page_url('resi')); ?>">Resi e rimborsi</a></li>
                    <li><a class="footer-link" href="<?php echo esc_url($ev_footer_page_url('faq')); ?>">FAQ</a></li>
                </ul>
            </nav>

            <nav class="footer-app__col" aria-label="Navigazione account e legale">
                <h6>Account</h6>
                <ul>
                    <li><a class="footer-link" href="<?php echo esc_url($ev_footer_page_url('account', wp_login_url())); ?>">Il mio account</a></li>
                    <li><a class="footer-link" href="<?php echo esc_url($ev_footer_page_url('carrello')); ?>">Carrello</a></li>
                    <li><a class="footer-link" href="<?php echo esc_url($ev_footer_page_url('wishlist')); ?>">Wishlist</a></li>
                    <li><a class="footer-link" href="<?php echo esc_url(home_url('/wp-login.php')); ?>">Accedi al gestionale</a></li>
                </ul>
            </nav>
        </div>

        <div class="footer-app__bottom">
            <small>&copy; <?php echo date('Y'); ?> E-vindemus. Tutti i diritti riservati.</small>
            <div class="footer-app__legal">
                <a class="footer-link" href="<?php echo esc_url($ev_footer_page_url('privacy-policy')); ?>">Privacy</a>
                <a class="footer-link" href="<?php echo esc_url($ev_footer_page_url('cookie-policy')); ?>">Cookie</a>
                <a class="footer-link" href="<?php echo esc_url($ev_footer_page_url('termini-e-condizioni')); ?>">Termini e condizioni</a>
            </div>
        </div>
    </div>
</footer>

<?php
$ev_mobile_search_page = get_page_by_path('ricerca-prodotti');
$ev_mobile_search_url = ($ev_mobile_search_page instanceof WP_Post)
    ? get_permalink($ev_mobile_search_page->ID)
    : home_url('/ricerca-prodotti/');
?>
<a class="ev-mobile-search-cta" href="<?php echo esc_url($ev_mobile_search_url); ?>" aria-label="Apri ricerca prodotti">
    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
    Cerca prodotti
</a>
<?php wp_footer(); ?>
</body>
</html>
