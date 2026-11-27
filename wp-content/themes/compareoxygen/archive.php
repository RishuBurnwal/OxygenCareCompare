<?php
/**
 * Generic archive (categories, tags, dates).
 *
 * @package compareoxygen
 */

get_header();

if (is_category()) {
    $title = single_cat_title('', false);
    $eyebrow = __('Guide category', 'compareoxygen');
    $description = category_description();
} elseif (is_tag()) {
    $title = single_tag_title('', false);
    $eyebrow = __('Tagged guides', 'compareoxygen');
    $description = tag_description();
} elseif (is_author()) {
    $title = get_the_author();
    $eyebrow = __('Author', 'compareoxygen');
    $description = get_the_author_meta('description');
} else {
    $title = wp_strip_all_tags(get_the_archive_title());
    $eyebrow = __('Archive', 'compareoxygen');
    $description = get_the_archive_description();
}
?>
<main id="main-content">
    <?php compareoxygen_breadcrumbs(); ?>

    <section class="page-hero page-hero--mint">
        <div class="container page-hero__inner">
            <p class="eyebrow"><?php echo esc_html($eyebrow); ?></p>
            <h1><?php echo esc_html($title); ?></h1>
            <?php if ($description) : ?>
                <div class="page-hero__lede"><?php echo wp_kses_post($description); ?></div>
            <?php else : ?>
                <p class="page-hero__lede"><?php esc_html_e('Buying guides and explainers from the CompareOxygen editorial team.', 'compareoxygen'); ?></p>
            <?php endif; ?>
            <div class="page-hero__meta">
                <span><?php echo compareoxygen_icon('book'); ?><?php echo esc_html(sprintf(_n('%s guide', '%s guides', (int) $GLOBALS['wp_query']->found_posts, 'compareoxygen'), number_format_i18n((int) $GLOBALS['wp_query']->found_posts))); ?></span>
                <span><a href="<?php echo esc_url(compareoxygen_page_url('guides')); ?>"><?php esc_html_e('All guides', 'compareoxygen'); ?></a></span>
            </div>
        </div>
    </section>

    <section class="section section--tight">
        <div class="container">
            <?php if (have_posts()) : ?>
                <div class="guide-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php $post_categories = get_the_category(); ?>
                        <article class="guide-card">
                            <div class="guide-card__visual"><?php echo compareoxygen_icon('book'); ?></div>
                            <div class="guide-card__body">
                                <p class="guide-card__cat"><?php echo esc_html($post_categories ? $post_categories[0]->name : __('Guide', 'compareoxygen')); ?></p>
                                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <p><?php echo esc_html(wp_trim_words(get_the_excerpt() ?: wp_strip_all_tags(get_the_content()), 22)); ?></p>
                                <div class="guide-card__meta">
                                    <span><?php printf(esc_html__('%d min read', 'compareoxygen'), compareoxygen_reading_time(get_the_content())); ?></span>
                                    <span><?php echo esc_html(get_the_date('M j, Y')); ?></span>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
                <div class="pagination">
                    <?php
                    the_posts_pagination([
                        'mid_size' => 1,
                        'prev_text' => __('Previous', 'compareoxygen'),
                        'next_text' => __('Next', 'compareoxygen'),
                        'screen_reader_text' => __('Archive pagination', 'compareoxygen'),
                    ]);
                    ?>
                </div>
            <?php else : ?>
                <div class="empty-state">
                    <h2><?php esc_html_e('Nothing filed here yet', 'compareoxygen'); ?></h2>
                    <p><?php esc_html_e('Try the full guide library instead.', 'compareoxygen'); ?></p>
                    <p><a class="btn btn--primary" href="<?php echo esc_url(compareoxygen_page_url('guides')); ?>"><?php esc_html_e('All buying guides', 'compareoxygen'); ?></a></p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php
get_footer();
