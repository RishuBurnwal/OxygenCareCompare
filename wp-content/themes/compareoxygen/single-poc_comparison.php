<?php
/**
 * Approved comparison page.
 *
 * @package compareoxygen
 */

get_header();

while (have_posts()) :
    the_post();
    $comparison_id = get_the_ID();
    [$first_id, $second_id] = COC_Core::comparison_ids($comparison_id);
    $first_post = get_post($first_id);
    $second_post = get_post($second_id);
    $prepared = compareoxygen_prepare_content(apply_filters('the_content', get_the_content()));
    $differences = ($first_id && $second_id && $first_id !== $second_id) ? COC_Core::difference_count($first_id, $second_id) : 0;
    $verified = ($first_id && $second_id) ? COC_Core::min_verified_between($first_id, $second_id) : '';
    $share_url = get_permalink();
    ?>
    <main id="main-content">
        <?php compareoxygen_breadcrumbs(); ?>

        <section class="page-hero page-hero--mint">
            <div class="container page-hero__inner">
                <p class="eyebrow"><?php esc_html_e('Editorial comparison', 'compareoxygen'); ?></p>
                <h1><?php the_title(); ?></h1>
                <p class="page-hero__lede"><?php echo esc_html(get_the_excerpt() ?: wp_trim_words(wp_strip_all_tags(get_the_content()), 30)); ?></p>
                <div class="page-hero__meta">
                    <?php if ($differences) : ?>
                        <span><?php echo compareoxygen_icon('list'); ?><?php printf(esc_html(_n('%s difference in published specifications', '%s differences in published specifications', $differences, 'compareoxygen')), esc_html(number_format_i18n($differences))); ?></span>
                    <?php endif; ?>
                    <?php if ($verified) : ?>
                        <span><?php echo compareoxygen_icon('check'); ?><?php printf(esc_html__('Specifications checked %s', 'compareoxygen'), esc_html(compareoxygen_format_date($verified))); ?></span>
                    <?php endif; ?>
                    <span><?php echo compareoxygen_icon('shield'); ?><?php esc_html_e('No ranking implied by order', 'compareoxygen'); ?></span>
                </div>
                <div class="button-row mt-3">
                    <?php if ($first_post instanceof WP_Post) : ?>
                        <a class="btn btn--primary btn--sm" href="<?php echo esc_url(get_permalink($first_post)); ?>"><?php echo esc_html($first_post->post_title); ?></a>
                    <?php endif; ?>
                    <?php if ($second_post instanceof WP_Post) : ?>
                        <a class="btn btn--secondary btn--sm" href="<?php echo esc_url(get_permalink($second_post)); ?>"><?php echo esc_html($second_post->post_title); ?></a>
                    <?php endif; ?>
                    <button class="btn btn--ghost btn--sm" type="button" data-share="<?php echo esc_url($share_url); ?>"><?php echo compareoxygen_icon('arrow-up-right'); ?><span data-share-label><?php esc_html_e('Copy link', 'compareoxygen'); ?></span></button>
                </div>
            </div>
        </section>

        <div class="container container--wide">
            <?php if (!$first_id || !$second_id || $first_id === $second_id || !$first_post instanceof WP_Post || !$second_post instanceof WP_Post) : ?>
                <div class="empty-state">
                    <h2><?php esc_html_e('This comparison is not linked to two models', 'compareoxygen'); ?></h2>
                    <p><?php esc_html_e('An editor needs to select both models before the evidence table can be generated. In the meantime, the comparison tool works with any two published records.', 'compareoxygen'); ?></p>
                    <p><a class="btn btn--primary" href="<?php echo esc_url(coc_compare_tool_url()); ?>"><?php esc_html_e('Open the comparison tool', 'compareoxygen'); ?></a></p>
                </div>
            <?php else : ?>
                <?php compareoxygen_disclosure(); ?>

                <div class="container container--reading" style="padding-inline:0;">
                    <div class="prose">
                        <?php if (trim($prepared['content']) !== '') : ?>
                            <?php echo wp_kses_post($prepared['content']); ?>
                        <?php else : ?>
                            <p><?php printf(esc_html__('This page compares the published specifications of the %1$s and the %2$s. Every confirmed value links to its manufacturer source; gaps stay visible.', 'compareoxygen'), esc_html($first_post->post_title), esc_html($second_post->post_title)); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mt-3">
                    <?php get_template_part('template-parts/compare', 'results', ['first' => $first_id, 'second' => $second_id]); ?>
                </div>

                <section class="section section--tight">
                    <div class="section-head">
                        <p class="eyebrow"><?php esc_html_e('How to use this comparison', 'compareoxygen'); ?></p>
                        <h2 class="section-title"><?php esc_html_e('Read the gaps, not just the differences', 'compareoxygen'); ?></h2>
                    </div>
                    <div class="feature-grid">
                        <article class="feature-cell">
                            <span class="feature-cell__icon"><?php echo compareoxygen_icon('scale'); ?></span>
                            <h3><?php esc_html_e('Equal settings, equal footing', 'compareoxygen'); ?></h3>
                            <p><?php esc_html_e('Published battery figures usually describe a low setting. If one manufacturer states its test setting and the other does not, the numbers are not directly comparable — and we say so.', 'compareoxygen'); ?></p>
                        </article>
                        <article class="feature-cell">
                            <span class="feature-cell__icon"><?php echo compareoxygen_icon('minus'); ?></span>
                            <h3><?php esc_html_e('“Not confirmed” is information', 'compareoxygen'); ?></h3>
                            <p><?php esc_html_e('A missing figure means the manufacturer does not publish it or we have not verified it. Do not read a blank as “bad” or as “average”. Ask the supplier directly.', 'compareoxygen'); ?></p>
                        </article>
                        <article class="feature-cell">
                            <span class="feature-cell__icon"><?php echo compareoxygen_icon('shield'); ?></span>
                            <h3><?php esc_html_e('Specifications are not a prescription', 'compareoxygen'); ?></h3>
                            <p><?php esc_html_e('The better-documented device is not automatically the clinically appropriate one. Flow setting, pulse dose, and oxygen needs are decided with your clinician.', 'compareoxygen'); ?></p>
                        </article>
                    </div>
                </section>

                <div class="related-panel">
                    <h2><?php esc_html_e('Related records', 'compareoxygen'); ?></h2>
                    <div class="related-links">
                        <a class="related-link" href="<?php echo esc_url(get_permalink($first_post)); ?>"><?php printf(esc_html__('%s review and specifications', 'compareoxygen'), esc_html($first_post->post_title)); ?><span><?php esc_html_e('Review', 'compareoxygen'); ?></span></a>
                        <a class="related-link" href="<?php echo esc_url(get_permalink($second_post)); ?>"><?php printf(esc_html__('%s review and specifications', 'compareoxygen'), esc_html($second_post->post_title)); ?><span><?php esc_html_e('Review', 'compareoxygen'); ?></span></a>
                        <a class="related-link" href="<?php echo esc_url(coc_compare_tool_url()); ?>"><?php esc_html_e('Build a different comparison', 'compareoxygen'); ?><span><?php esc_html_e('Comparison tool', 'compareoxygen'); ?></span></a>
                    </div>
                </div>

                <?php compareoxygen_medical_note(); ?>
            <?php endif; ?>
        </div>
    </main>
    <?php
endwhile;

get_footer();
