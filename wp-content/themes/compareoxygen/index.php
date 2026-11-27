<?php
/**
 * Posts index (guides).
 *
 * @package compareoxygen
 */

get_header();

$page_for_posts = (int) get_option('page_for_posts');
$title = $page_for_posts ? get_the_title($page_for_posts) : __('Buying guides and explainers', 'compareoxygen');
$intro = $page_for_posts ? get_post_field('post_excerpt', $page_for_posts) : '';
$categories = get_categories(['hide_empty' => true, 'number' => 8]);
?>
<main id="main-content">
    <?php compareoxygen_breadcrumbs(); ?>

    <section class="page-hero page-hero--mint">
        <div class="container page-hero__inner">
            <p class="eyebrow"><?php esc_html_e('Research library', 'compareoxygen'); ?></p>
            <h1><?php echo esc_html($title); ?></h1>
            <p class="page-hero__lede"><?php echo esc_html($intro ?: __('Explainers on oxygen delivery, battery claims, travel rules, and the parts of a specification sheet that are easy to misread. Educational reading, not medical advice.', 'compareoxygen')); ?></p>
            <div class="page-hero__meta">
                <span><?php echo compareoxygen_icon('shield'); ?><?php esc_html_e('Written by the editorial team', 'compareoxygen'); ?></span>
                <span><?php echo compareoxygen_icon('file'); ?><?php esc_html_e('Sources cited in the text', 'compareoxygen'); ?></span>
            </div>
        </div>
    </section>

    <?php if ($categories) : ?>
        <div class="container section--tight">
            <div class="filter-bar" aria-label="<?php esc_attr_e('Guide categories', 'compareoxygen'); ?>">
                <span class="filter-bar__label"><?php esc_html_e('Topics', 'compareoxygen'); ?></span>
                <a class="filter-chip is-current" href="<?php echo esc_url($page_for_posts ? get_permalink($page_for_posts) : home_url('/')); ?>"><?php esc_html_e('All', 'compareoxygen'); ?></a>
                <?php foreach ($categories as $category) : ?>
                    <a class="filter-chip" href="<?php echo esc_url(get_category_link($category)); ?>"><?php echo esc_html($category->name); ?><span class="muted">(<?php echo esc_html(number_format_i18n($category->count)); ?>)</span></a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

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
                        'screen_reader_text' => __('Guides pagination', 'compareoxygen'),
                    ]);
                    ?>
                </div>
            <?php else : ?>
                <div class="empty-state">
                    <h2><?php esc_html_e('No guides published yet', 'compareoxygen'); ?></h2>
                    <p><?php esc_html_e('Our first explainers are in editorial review. The methodology page describes how we verify specifications in the meantime.', 'compareoxygen'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <div class="container">
        <?php
        compareoxygen_cta_band(
            __('Reading is the first filter', 'compareoxygen'),
            __('When you are ready for a shortlist, the comparison tool shows exactly what two manufacturers publish — and what they do not.', 'compareoxygen'),
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
