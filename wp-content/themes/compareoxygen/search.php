<?php
/**
 * Search results.
 *
 * @package compareoxygen
 */

get_header();

$query = get_search_query();
$total = (int) $GLOBALS['wp_query']->found_posts;
?>
<main id="main-content">
    <?php compareoxygen_breadcrumbs(); ?>

    <section class="page-hero page-hero--mint">
        <div class="container page-hero__inner">
            <p class="eyebrow"><?php esc_html_e('Search', 'compareoxygen'); ?></p>
            <h1><?php printf(esc_html__('Results for “%s”', 'compareoxygen'), esc_html($query)); ?></h1>
            <p class="page-hero__lede"><?php echo esc_html(sprintf(_n('%s matching page across reviews, comparisons, and guides.', '%s matching pages across reviews, comparisons, and guides.', $total, 'compareoxygen'), number_format_i18n($total))); ?></p>
            <form class="search-inline" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <label class="screen-reader-text" for="search-again"><?php esc_html_e('Search again', 'compareoxygen'); ?></label>
                <input id="search-again" type="search" name="s" value="<?php echo esc_attr($query); ?>" placeholder="<?php esc_attr_e('Search reviews, comparisons, and guides', 'compareoxygen'); ?>">
                <button class="btn btn--primary" type="submit"><?php echo compareoxygen_icon('search'); ?><?php esc_html_e('Search', 'compareoxygen'); ?></button>
            </form>
        </div>
    </section>

    <section class="section section--tight">
        <div class="container container--reading">
            <?php if (have_posts()) : ?>
                <div class="search-results">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php
                        $type = get_post_type();
                        $label = __('Guide', 'compareoxygen');
                        if ($type === 'poc_model') { $label = __('Model review', 'compareoxygen'); }
                        elseif ($type === 'poc_comparison') { $label = __('Comparison', 'compareoxygen'); }
                        elseif ($type === 'page') { $label = __('Page', 'compareoxygen'); }
                        ?>
                        <article class="search-result">
                            <p class="search-result__meta"><?php echo esc_html($label); ?><?php if ($type === 'poc_model') : ?> · <?php echo esc_html(coc_brand_of(get_the_ID())); ?><?php endif; ?></p>
                            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <p><?php echo esc_html(wp_trim_words(get_the_excerpt() ?: wp_strip_all_tags(get_the_content()), 26)); ?></p>
                        </article>
                    <?php endwhile; ?>
                </div>
                <div class="pagination">
                    <?php
                    the_posts_pagination([
                        'mid_size' => 1,
                        'prev_text' => __('Previous', 'compareoxygen'),
                        'next_text' => __('Next', 'compareoxygen'),
                        'screen_reader_text' => __('Search pagination', 'compareoxygen'),
                    ]);
                    ?>
                </div>
            <?php else : ?>
                <div class="empty-state">
                    <h2><?php esc_html_e('No pages matched that search', 'compareoxygen'); ?></h2>
                    <p><?php esc_html_e('Try a brand (Inogen, OxyGo, CAIRE), a model name, or a specification such as “FAA” or “battery”. The comparison tool is also a faster way to find a specific model.', 'compareoxygen'); ?></p>
                    <p><a class="btn btn--primary" href="<?php echo esc_url(coc_compare_tool_url()); ?>"><?php esc_html_e('Open the comparison tool', 'compareoxygen'); ?></a></p>
                </div>
            <?php endif; ?>

            <div class="related-panel">
                <h2><?php esc_html_e('Popular destinations', 'compareoxygen'); ?></h2>
                <div class="related-links">
                    <a class="related-link" href="<?php echo esc_url(get_post_type_archive_link('poc_model') ?: home_url('/reviews/')); ?>"><?php esc_html_e('All model reviews', 'compareoxygen'); ?><span><?php esc_html_e('Reviews', 'compareoxygen'); ?></span></a>
                    <a class="related-link" href="<?php echo esc_url(compareoxygen_page_url('guides')); ?>"><?php esc_html_e('Buying guides', 'compareoxygen'); ?><span><?php esc_html_e('Guides', 'compareoxygen'); ?></span></a>
                    <a class="related-link" href="<?php echo esc_url(compareoxygen_page_url('best', '/best/portable-oxygen-concentrators/')); ?>"><?php esc_html_e('Best portable oxygen concentrators', 'compareoxygen'); ?><span><?php esc_html_e('Awards', 'compareoxygen'); ?></span></a>
                </div>
            </div>
        </div>
    </section>
</main>
<?php
get_footer();
