<?php
/**
 * Single buying guide / article.
 *
 * @package compareoxygen
 */

get_header();

while (have_posts()) :
    the_post();
    $post_id = get_the_ID();
    $prepared = compareoxygen_prepare_content(apply_filters('the_content', get_the_content()));
    $categories = get_the_category($post_id);
    $primary_category = $categories ? $categories[0] : null;
    $reading = compareoxygen_reading_time(get_the_content());
    $toc = $prepared['toc'];
    $toc[] = ['id' => 'how-produced', 'title' => __('How this guide was produced', 'compareoxygen')];
    $related_args = [
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => 3,
        'post__not_in' => [$post_id],
        'ignore_sticky_posts' => true,
    ];
    if ($primary_category) { $related_args['cat'] = $primary_category->term_id; }
    $related = get_posts($related_args);
    if (!$related) {
        $related = get_posts(['post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 3, 'post__not_in' => [$post_id]]);
    }
    ?>
    <main id="main-content">
        <?php compareoxygen_breadcrumbs(); ?>

        <section class="page-hero">
            <div class="container page-hero__inner">
                <?php if ($primary_category) : ?>
                    <p class="eyebrow"><a href="<?php echo esc_url(get_category_link($primary_category)); ?>" style="color:inherit;text-decoration:none;"><?php echo esc_html($primary_category->name); ?></a></p>
                <?php else : ?>
                    <p class="eyebrow"><?php esc_html_e('Buying guide', 'compareoxygen'); ?></p>
                <?php endif; ?>
                <h1><?php the_title(); ?></h1>
                <?php if (has_excerpt()) : ?>
                    <p class="page-hero__lede"><?php echo esc_html(get_the_excerpt()); ?></p>
                <?php endif; ?>
                <div class="page-hero__meta">
                    <span><?php echo compareoxygen_icon('file'); ?><?php echo esc_html(get_the_author() ?: __('CompareOxygen Editorial Team', 'compareoxygen')); ?></span>
                    <span><?php echo compareoxygen_icon('clock'); ?><?php printf(esc_html__('%d min read', 'compareoxygen'), (int) $reading); ?></span>
                    <span><?php echo compareoxygen_icon('check'); ?><?php printf(esc_html__('Published %s', 'compareoxygen'), esc_html(get_the_date('F j, Y'))); ?></span>
                    <?php if (get_the_modified_date('Y-m-d') !== get_the_date('Y-m-d')) : ?>
                        <span><?php printf(esc_html__('Updated %s', 'compareoxygen'), esc_html(get_the_modified_date('F j, Y'))); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <div class="container guide-layout">
            <?php compareoxygen_toc($toc, coc_compare_tool_url(), __('Compare concentrators', 'compareoxygen')); ?>

            <div>
                <?php compareoxygen_disclosure(); ?>

                <article class="article">
                    <?php echo wp_kses_post($prepared['content']); ?>
                </article>

                <section class="section--tight" id="how-produced">
                    <div class="verdict-card">
                        <p class="eyebrow"><?php esc_html_e('Editorial note', 'compareoxygen'); ?></p>
                        <h2><?php esc_html_e('How this guide was produced', 'compareoxygen'); ?></h2>
                        <p><?php esc_html_e('This guide was written by the CompareOxygenConcentrators.com editorial team from manufacturer documentation, published specifications, and the sources cited inside the article. It is general education, not medical advice, and it is not a substitute for an assessment by your clinician or oxygen supplier.', 'compareoxygen'); ?></p>
                        <p class="muted small mb-0"><?php esc_html_e('Commercial relationships do not determine which models we discuss or what we conclude. If a guide is ever based on hands-on measurement, the method and date will be stated in the text.', 'compareoxygen'); ?></p>
                        <div class="button-row mt-3">
                            <a class="btn btn--secondary btn--sm" href="<?php echo esc_url(compareoxygen_page_url('how-we-rate')); ?>"><?php esc_html_e('How we rate', 'compareoxygen'); ?><?php echo compareoxygen_icon('arrow-right'); ?></a>
                            <a class="btn btn--ghost btn--sm" href="<?php echo esc_url(compareoxygen_page_url('contact')); ?>"><?php esc_html_e('Suggest a correction', 'compareoxygen'); ?></a>
                        </div>
                    </div>
                </section>

                <?php if ($related) : ?>
                    <div class="related-panel">
                        <h2><?php esc_html_e('Continue reading', 'compareoxygen'); ?></h2>
                        <div class="related-links">
                            <?php foreach ($related as $item) : ?>
                                <a class="related-link" href="<?php echo esc_url(get_permalink($item)); ?>"><?php echo esc_html(get_the_title($item)); ?><span><?php printf(esc_html__('%d min read', 'compareoxygen'), compareoxygen_reading_time($item->post_content)); ?></span></a>
                            <?php endforeach; ?>
                            <a class="related-link" href="<?php echo esc_url(compareoxygen_page_url('guides')); ?>"><?php esc_html_e('All buying guides', 'compareoxygen'); ?><span><?php esc_html_e('Guide library', 'compareoxygen'); ?></span></a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php compareoxygen_medical_note(); ?>
            </div>
        </div>

        <div class="container">
            <?php
            compareoxygen_cta_band(
                __('Turn the research into a side-by-side answer', 'compareoxygen'),
                __('Choose two models and read only what the manufacturers publish — including the fields they leave blank.', 'compareoxygen'),
                __('Compare concentrators', 'compareoxygen'),
                coc_compare_tool_url(),
                __('See the Best Of guide', 'compareoxygen'),
                compareoxygen_page_url('best', '/best/portable-oxygen-concentrators/')
            );
            ?>
        </div>
    </main>
    <?php
endwhile;

get_footer();
