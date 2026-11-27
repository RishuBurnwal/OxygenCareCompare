<?php
/**
 * Template Name: Guides hub
 * Description: Library of buying guides and explainers.
 *
 * @package compareoxygen
 */

get_header();

$guides = get_posts(['post_type' => 'post', 'post_status' => 'publish', 'numberposts' => 12]);
$categories = get_categories(['hide_empty' => true, 'number' => 8]);
$prepared = compareoxygen_prepare_content(apply_filters('the_content', get_post_field('post_content', get_the_ID())));
?>
<main id="main-content">
    <?php compareoxygen_breadcrumbs(); ?>

    <section class="page-hero page-hero--mint">
        <div class="container page-hero__inner">
            <p class="eyebrow"><?php esc_html_e('Buying guides', 'compareoxygen'); ?></p>
            <h1><?php esc_html_e('Learn the questions before the shortlist', 'compareoxygen'); ?></h1>
            <p class="page-hero__lede"><?php esc_html_e('Plain-English explainers on flow settings, battery claims, airline rules, warranty coverage, and the parts of a specification sheet that are easy to misread.', 'compareoxygen'); ?></p>
            <div class="page-hero__meta">
                <span><?php echo compareoxygen_icon('book'); ?><?php echo esc_html(sprintf(_n('%s guide published', '%s guides published', count($guides), 'compareoxygen'), number_format_i18n(count($guides)))); ?></span>
                <span><?php echo compareoxygen_icon('shield'); ?><?php esc_html_e('Educational, not medical advice', 'compareoxygen'); ?></span>
            </div>
        </div>
    </section>

    <?php if (trim($prepared['content']) !== '') : ?>
        <div class="container container--reading section--tight">
            <div class="prose"><?php echo wp_kses_post($prepared['content']); ?></div>
        </div>
    <?php endif; ?>

    <section class="section section--tight">
        <div class="container">
            <?php if ($categories) : ?>
                <div class="filter-bar" aria-label="<?php esc_attr_e('Guide categories', 'compareoxygen'); ?>">
                    <span class="filter-bar__label"><?php esc_html_e('Browse', 'compareoxygen'); ?></span>
                    <a class="filter-chip is-current" href="<?php echo esc_url(compareoxygen_page_url('guides')); ?>"><?php esc_html_e('All guides', 'compareoxygen'); ?></a>
                    <?php foreach ($categories as $category) : ?>
                        <a class="filter-chip" href="<?php echo esc_url(get_category_link($category)); ?>"><?php echo esc_html($category->name); ?><span class="muted">(<?php echo esc_html(number_format_i18n($category->count)); ?>)</span></a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($guides) : ?>
                <div class="guide-grid">
                    <?php foreach ($guides as $guide) : ?>
                        <?php $guide_categories = get_the_category($guide->ID); ?>
                        <article class="guide-card">
                            <div class="guide-card__visual"><?php echo compareoxygen_icon('book'); ?></div>
                            <div class="guide-card__body">
                                <p class="guide-card__cat"><?php echo esc_html($guide_categories ? $guide_categories[0]->name : __('Guide', 'compareoxygen')); ?></p>
                                <h3><a href="<?php echo esc_url(get_permalink($guide)); ?>"><?php echo esc_html(get_the_title($guide)); ?></a></h3>
                                <p><?php echo esc_html(wp_trim_words($guide->post_excerpt ?: wp_strip_all_tags($guide->post_content), 22)); ?></p>
                                <div class="guide-card__meta">
                                    <span><?php printf(esc_html__('%d min read', 'compareoxygen'), compareoxygen_reading_time($guide->post_content)); ?></span>
                                    <span><?php echo esc_html(get_the_date('M j, Y', $guide)); ?></span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="empty-state">
                    <h2><?php esc_html_e('Guides are being prepared', 'compareoxygen'); ?></h2>
                    <p><?php esc_html_e('Our first explainers are in editorial review. In the meantime, the methodology page describes how specifications are verified.', 'compareoxygen'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="section section--white">
        <div class="container">
            <div class="section-head">
                <p class="eyebrow"><?php esc_html_e('Where to start', 'compareoxygen'); ?></p>
                <h2 class="section-title"><?php esc_html_e('Three questions that narrow the field fastest', 'compareoxygen'); ?></h2>
            </div>
            <div class="steps">
                <article class="step">
                    <h3><?php esc_html_e('What flow does the prescription require?', 'compareoxygen'); ?></h3>
                    <p><?php esc_html_e('Pulse-dose settings and continuous-flow litres per minute are not equivalent. Establish the prescribed mode and setting before comparing devices.', 'compareoxygen'); ?></p>
                </article>
                <article class="step">
                    <h3><?php esc_html_e('How long are you away from power?', 'compareoxygen'); ?></h3>
                    <p><?php esc_html_e('Compare battery figures at the same setting, and account for charging time. Published runtimes usually describe the lowest setting.', 'compareoxygen'); ?></p>
                </article>
                <article class="step">
                    <h3><?php esc_html_e('Who carries it, and for how long?', 'compareoxygen'); ?></h3>
                    <p><?php esc_html_e('Weight with the battery fitted matters more than a bare device figure. Check the carrying bag and whether an extended battery changes the balance.', 'compareoxygen'); ?></p>
                </article>
            </div>
        </div>
    </section>

    <div class="container">
        <?php
        compareoxygen_cta_band(
            __('Put the reading to work', 'compareoxygen'),
            __('Open the comparison tool and look at two specification records side by side. The gaps are as useful as the numbers.', 'compareoxygen'),
            __('Compare concentrators', 'compareoxygen'),
            coc_compare_tool_url(),
            __('See the Best Of guide', 'compareoxygen'),
            compareoxygen_page_url('best', '/best/portable-oxygen-concentrators/')
        );
        ?>
    </div>
</main>
<?php
get_footer();
