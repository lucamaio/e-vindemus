<section class="ev-home" aria-label="Tutti i prodotti">
    <div class="ev-home__container">
          <section class="ev-products">
            <div class="ev-products__head">
                <h2>Tutti i prodotti</h2>
                <p>Esplora la nostra vasta gamma di prodotti, dalle ultime novità ai best seller e alle offerte del momento.</p>
            </div>
            <?php //get_template_part('template-parts/home/mostra-articoli', 'home'); ?>
            <div class ="ev-products__grid">
                <?php get_template_part('template-parts/prodotto/card-list', 'home'); ?>
            </div>
          </section>
    </div>
</section>