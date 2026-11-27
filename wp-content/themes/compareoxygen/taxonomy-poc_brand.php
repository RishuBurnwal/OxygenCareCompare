<?php
/**
 * Brand archive.
 *
 * @package compareoxygen
 */

get_header();

$term = get_queried_object();
if (!$term instanceof WP_Term) { get_footer(); return; }

$models = coc_models([
    'numberposts' => -1,
    'tax_query' => [['taxonomy' => 'poc_brand', 'field' => 'term_id', 'terms' => $term->term_id]],
]);
$total_facts = 0;
$latest = '';
foreach ($models as $model) {
    $total_facts += coc_verified_count($model->ID);
    $checked = coc_last_verified($model->ID);
    if ($checked > $latest) { $latest = $checked; }
}
$other_brands = get_terms([
    'taxonomy' => 'poc_brand',
    'hide_empty' => true,
    'exclude' => [$term->term_id],
    'number' => 8,
]);
?>
<main id="main-content">
    <?php compareoxygen_breadcrumbs(); ?>

    <section class="brand-hero">
        <div class="container">
            <div class="brand-hero__inner">
                <?php compareoxygen_brand_mark($term->name, 'lg'); ?>
                <div>
                    <p class="eyebrow"><?php esc_html_e('Manufacturer', 'compareoxygen'); ?></p>
                    <h1><?php printf(esc_html__('%s portable oxygen concentrators', 'compareoxygen'), esc_html($term->name)); ?></h1>
                    <p><?php echo esc_html($term->description ?: sprintf(__('Every %s model whose specifications we have verified from the manufacturer’s own documentation.', 'compareoxygen'), $term->name)); ?></p>
                    <div class="brand-stats">
                        <div><strong><?php echo esc_html(number_format_i18n(count($models))); ?></strong><?php esc_html_e('reviewed models', 'compareoxygen'); ?></div>
                        <div><strong><?php echo esc_html(number_format_i18n($total_facts)); ?></strong><?php esc_html_e('source-verified facts', 'compareoxygen'); ?></div>
                        <?php if ($latest) : ?><div><strong><?php echo esc_html(compareoxygen_format_date($latest)); ?></strong><?php esc_html_e('latest source check', 'compareoxygen'); ?></div><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <?php compareoxygen_disclosure(); ?>
    </div>

    <section class="section section--tight">
        <div class="container">
            <?php if ($models) : ?>
                <div class="product-grid">
                    <?php foreach ($models as $model) : ?>
                        <?php compareoxygen_product_card($model->ID); ?>
                    <?php endforeach; ?>
                </div>

                <div class="section-head mt-4">
                    <p class="eyebrow"><?php esc_html_e('Compare directly', 'compareoxygen'); ?></p>
                    <h2 class="section-title"><?php printf(esc_html__('Put a %s model beside another', 'compareoxygen'), esc_html($term->name)); ?></h2>
                    <p class="section-intro"><?php esc_html_e('Each link opens the comparison tool with this model already selected. Choose a second model to see the published differences.', 'compareoxygen'); ?></p>
                </div>
                <div class="related-links">
                    <?php foreach (array_slice($models, 0, 3) as $model) : ?>
                        <a class="related-link" href="<?php echo esc_url(add_query_arg(['first' => $model->ID], coc_compare_tool_url())); ?>"><?php printf(esc_html__('Compare the %s', 'compareoxygen'), esc_html(get_the_title($model))); ?><span><?php esc_html_e('Choose a second model', 'compareoxygen'); ?></span></a>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="empty-state">
                    <h2><?php esc_html_e('No published models for this brand yet', 'compareoxygen'); ?></h2>
                    <p><?php esc_html_e('This brand page is visible because the manufacturer exists in our review queue, but no model record has passed verification. We do not publish empty marketing profiles in the meantime.', 'compareoxygen'); ?></p>
                    <p><a class="btn btn--primary" href="<?php echo esc_url(get_post_type_archive_link('poc_model') ?: home_url('/reviews/')); ?>"><?php esc_html_e('Browse reviewed models', 'compareoxygen'); ?></a></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="section section--white">
        <div class="container">
            <div class="compare-note">
                <?php echo compareoxygen_icon('info'); ?>
                <p><?php esc_html_e('Inclusion on this page is not an endorsement of the manufacturer, and the order of cards is not a ranking. Cards are ordered by the editorial menu order, which exists to keep related models together — not to imply a winner.', 'compareoxygen'); ?></p>
            </div>

            <?php if (!is_wp_error($other_brands) && $other_brands) : ?>
                <div class="section-head mt-4">
                    <p class="eyebrow"><?php esc_html_e('Other manufacturers', 'compareoxygen'); ?></p>
                    <h2 class="section-title"><?php esc_html_e('Keep browsing the index', 'compareoxygen'); ?></h2>
                </div>
                <div class="filter-bar">
                    <?php foreach ($other_brands as $brand) : ?>
                        <a class="filter-chip" href="<?php echo esc_url(get_term_link($brand)); ?>"><?php echo esc_html($brand->name); ?><span class="muted">(<?php echo esc_html(number_format_i18n($brand->count)); ?>)</span></a>
                    <?php endforeach; ?>
                    <a class="filter-chip" href="<?php echo esc_url(compareoxygen_page_url('brands')); ?>"><?php esc_html_e('All brands', 'compareoxygen'); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <div class="container">
        <?php
        compareoxygen_cta_band(
            __('Need a different starting point?', 'compareoxygen'),
            __('Browse the full review library or start with the comparison categories for travel, daily use, and lightweight devices.', 'compareoxygen'),
            __('All model reviews', 'compareoxygen'),
            get_post_type_archive_link('poc_model') ?: home_url('/reviews/'),
            __('Compare concentrators', 'compareoxygen'),
            coc_compare_tool_url()
        );
        ?>
    </div>
</main>
<?php
get_footer();
