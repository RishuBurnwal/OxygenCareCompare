<?php
/**
 * Model archive (review library).
 *
 * @package compareoxygen
 */

get_header();

$brands = get_terms(['taxonomy' => 'poc_brand', 'hide_empty' => true]);
$use_cases = get_terms(['taxonomy' => 'poc_use_case', 'hide_empty' => true]);
$total = (int) $GLOBALS['wp_query']->found_posts;
$latest = '';
foreach (coc_models() as $model) {
    $checked = coc_last_verified($model->ID);
    if ($checked > $latest) { $latest = $checked; }
}
?>
<main id="main-content">
    <?php compareoxygen_breadcrumbs(); ?>

    <section class="page-hero page-hero--mint">
        <div class="container page-hero__inner">
            <p class="eyebrow"><?php esc_html_e('Review library', 'compareoxygen'); ?></p>
            <h1><?php esc_html_e('Portable oxygen concentrator reviews', 'compareoxygen'); ?></h1>
            <p class="page-hero__lede"><?php esc_html_e('Every model we have documented, with the specifications the manufacturer publishes, the source for each fact, and the date our editor checked it. Anything unverified is labelled rather than estimated.', 'compareoxygen'); ?></p>
            <div class="page-hero__meta">
                <span><?php echo compareoxygen_icon('layers'); ?><?php echo esc_html(sprintf(_n('%s published record', '%s published records', $total, 'compareoxygen'), number_format_i18n($total))); ?></span>
                <?php if ($latest) : ?>
                    <span><?php echo compareoxygen_icon('clock'); ?><?php printf(esc_html__('Latest source check %s', 'compareoxygen'), esc_html(compareoxygen_format_date($latest))); ?></span>
                <?php endif; ?>
                <span><?php echo compareoxygen_icon('shield'); ?><?php esc_html_e('No sponsored reviews', 'compareoxygen'); ?></span>
            </div>
        </div>
    </section>

    <div class="container">
        <?php compareoxygen_disclosure(); ?>
    </div>

    <section class="section section--tight">
        <div class="container">
            <?php if (!is_wp_error($brands) && $brands) : ?>
                <div class="filter-bar" aria-label="<?php esc_attr_e('Filter by brand', 'compareoxygen'); ?>">
                    <span class="filter-bar__label"><?php esc_html_e('Brands', 'compareoxygen'); ?></span>
                    <?php foreach ($brands as $brand) : ?>
                        <a class="filter-chip" href="<?php echo esc_url(get_term_link($brand)); ?>"><?php echo esc_html($brand->name); ?><span class="muted">(<?php echo esc_html(number_format_i18n($brand->count)); ?>)</span></a>
                    <?php endforeach; ?>
                    <a class="filter-chip" href="<?php echo esc_url(compareoxygen_page_url('brands')); ?>"><?php esc_html_e('All brands', 'compareoxygen'); ?></a>
                </div>
            <?php endif; ?>

            <?php if (have_posts()) : ?>
                <div class="product-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php compareoxygen_product_card(get_the_ID()); ?>
                    <?php endwhile; ?>
                </div>

                <div class="pagination">
                    <?php
                    the_posts_pagination([
                        'mid_size' => 1,
                        'prev_text' => __('Previous', 'compareoxygen'),
                        'next_text' => __('Next', 'compareoxygen'),
                        'screen_reader_text' => __('Reviews pagination', 'compareoxygen'),
                    ]);
                    ?>
                </div>
            <?php else : ?>
                <div class="empty-state">
                    <h2><?php esc_html_e('No reviews published yet', 'compareoxygen'); ?></h2>
                    <p><?php esc_html_e('Model records appear once their specifications have been verified against manufacturer sources. We do not publish placeholder reviews.', 'compareoxygen'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if (!is_wp_error($use_cases) && $use_cases) : ?>
        <section class="section section--white">
            <div class="container">
                <div class="section-head">
                    <p class="eyebrow"><?php esc_html_e('Browse by situation', 'compareoxygen'); ?></p>
                    <h2 class="section-title"><?php esc_html_e('Narrow the library to your week', 'compareoxygen'); ?></h2>
                </div>
                <div class="use-case-grid">
                    <?php
                    $case_icons = ['travel' => 'plane', 'daily-use' => 'route', 'lightweight' => 'weight', 'backup' => 'battery'];
                    foreach ($use_cases as $term) :
                        ?>
                        <a class="use-case" href="<?php echo esc_url(get_term_link($term)); ?>">
                            <span class="use-case__icon"><?php echo compareoxygen_icon($case_icons[$term->slug] ?? 'route'); ?></span>
                            <span>
                                <h3><?php echo esc_html($term->name); ?></h3>
                                <p><?php echo esc_html($term->description ?: __('Models documented for this context.', 'compareoxygen')); ?></p>
                            </span>
                            <span class="use-case__arrow"><?php echo compareoxygen_icon('arrow-up-right'); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <div class="container">
        <?php
        compareoxygen_cta_band(
            __('Two records, one table', 'compareoxygen'),
            __('Pick any two reviewed models and see every published specification side by side — with the gaps left visible.', 'compareoxygen'),
            __('Compare concentrators', 'compareoxygen'),
            coc_compare_tool_url(),
            __('How we rate', 'compareoxygen'),
            compareoxygen_page_url('how-we-rate')
        );
        ?>
    </div>
</main>
<?php
get_footer();
