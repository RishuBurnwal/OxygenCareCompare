<?php
/**
 * 404 page.
 *
 * @package compareoxygen
 */

get_header();

$models = coc_models(['numberposts' => 3]);
?>
<main id="main-content">
    <?php compareoxygen_breadcrumbs(); ?>

    <section class="page-hero page-hero--mint">
        <div class="container page-hero__inner">
            <p class="eyebrow"><?php esc_html_e('Error 404', 'compareoxygen'); ?></p>
            <h1><?php esc_html_e('That page is not here', 'compareoxygen'); ?></h1>
            <p class="page-hero__lede"><?php esc_html_e('The link may be outdated, or the page may never have existed. Nothing was lost from your comparison — start again below, or search the research library.', 'compareoxygen'); ?></p>
            <form class="search-inline" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <label class="screen-reader-text" for="search-404"><?php esc_html_e('Search the site', 'compareoxygen'); ?></label>
                <input id="search-404" type="search" name="s" placeholder="<?php esc_attr_e('Search reviews, comparisons, and guides', 'compareoxygen'); ?>">
                <button class="btn btn--primary" type="submit"><?php echo compareoxygen_icon('search'); ?><?php esc_html_e('Search', 'compareoxygen'); ?></button>
            </form>
            <div class="button-row mt-3">
                <a class="btn btn--primary" href="<?php echo esc_url(coc_compare_tool_url()); ?>"><?php echo compareoxygen_icon('scale'); ?><?php esc_html_e('Compare concentrators', 'compareoxygen'); ?></a>
                <a class="btn btn--secondary" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Back to the homepage', 'compareoxygen'); ?></a>
            </div>
        </div>
    </section>

    <section class="section section--tight">
        <div class="container">
            <div class="filter-bar" aria-label="<?php esc_attr_e('Useful destinations', 'compareoxygen'); ?>">
                <span class="filter-bar__label"><?php esc_html_e('Popular', 'compareoxygen'); ?></span>
                <a class="filter-chip" href="<?php echo esc_url(get_post_type_archive_link('poc_model') ?: home_url('/reviews/')); ?>"><?php esc_html_e('Model reviews', 'compareoxygen'); ?></a>
                <a class="filter-chip" href="<?php echo esc_url(compareoxygen_page_url('guides')); ?>"><?php esc_html_e('Buying guides', 'compareoxygen'); ?></a>
                <a class="filter-chip" href="<?php echo esc_url(compareoxygen_page_url('best', '/best/portable-oxygen-concentrators/')); ?>"><?php esc_html_e('Best Of', 'compareoxygen'); ?></a>
                <a class="filter-chip" href="<?php echo esc_url(compareoxygen_page_url('brands')); ?>"><?php esc_html_e('Brands', 'compareoxygen'); ?></a>
                <a class="filter-chip" href="<?php echo esc_url(compareoxygen_page_url('how-we-rate')); ?>"><?php esc_html_e('How we rate', 'compareoxygen'); ?></a>
            </div>

            <?php if ($models) : ?>
                <div class="section-head mt-4">
                    <p class="eyebrow"><?php esc_html_e('Recently documented', 'compareoxygen'); ?></p>
                    <h2 class="section-title"><?php esc_html_e('Start with a verified record', 'compareoxygen'); ?></h2>
                </div>
                <div class="product-grid">
                    <?php foreach ($models as $model) : ?>
                        <?php compareoxygen_product_card($model->ID); ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php
get_footer();
