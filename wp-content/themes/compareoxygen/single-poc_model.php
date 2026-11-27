<?php
/**
 * Single model review.
 *
 * @package compareoxygen
 */

get_header();

while (have_posts()) :
    the_post();
    $model_id = get_the_ID();
    $brand = coc_brand_of($model_id);
    $brand_link = coc_brand_link($model_id);
    $award = coc_award_label($model_id);
    $verified = coc_verified_count($model_id);
    $last_verified = coc_last_verified($model_id);
    $best_for = (string) get_post_meta($model_id, '_coc_best_for', true);
    $model_number = (string) get_post_meta($model_id, '_coc_model_number', true);
    $evidence_level = (string) get_post_meta($model_id, '_coc_evidence_level', true);
    $pros = coc_pros($model_id);
    $cons = coc_cons($model_id);
    $prepared = compareoxygen_prepare_content(apply_filters('the_content', get_the_content()));
    $compare_url = add_query_arg(['first' => $model_id], coc_compare_tool_url());

    $quick_keys = [
        'weight' => __('Weight', 'compareoxygen'),
        'battery_extended' => __('Battery, extended', 'compareoxygen'),
        'settings' => __('Flow settings', 'compareoxygen'),
        'sound_level' => __('Sound level', 'compareoxygen'),
        'flow_type' => __('Oxygen delivery', 'compareoxygen'),
        'warranty' => __('Warranty', 'compareoxygen'),
    ];

    $toc = $prepared['toc'];
    $toc[] = ['id' => 'specifications', 'title' => __('Full specifications', 'compareoxygen')];
    $toc[] = ['id' => 'verdict', 'title' => __('Editorial verdict', 'compareoxygen')];
    if ($pros || $cons) {
        $toc[] = ['id' => 'strengths', 'title' => __('Strengths and limitations', 'compareoxygen')];
    }
    $toc[] = ['id' => 'sources', 'title' => __('Sources and verification', 'compareoxygen')];
    ?>
    <main id="main-content">
        <?php compareoxygen_breadcrumbs(); ?>

        <section class="review-hero">
            <div class="container review-hero__grid">
                <div class="review-media">
                    <?php if ($award) : ?>
                        <span class="badge badge--award"><?php echo compareoxygen_icon('spark'); ?><?php echo esc_html($award); ?></span>
                    <?php endif; ?>
                    <?php compareoxygen_device_illustration('standard', $verified > 14 ? 'navy' : 'teal'); ?>
                    <span class="review-media__note"><?php esc_html_e('Technical illustration · not a product photo', 'compareoxygen'); ?></span>
                </div>

                <div class="review-head">
                    <?php if ($brand) : ?>
                        <p class="review-head__brand">
                            <?php if ($brand_link) : ?>
                                <a href="<?php echo esc_url($brand_link); ?>"><?php echo esc_html($brand); ?></a>
                            <?php else : ?>
                                <span class="product-card__brand"><?php echo esc_html($brand); ?></span>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                    <h1><?php the_title(); ?></h1>
                    <p class="review-head__lede"><?php echo esc_html(get_the_excerpt() ?: wp_trim_words(wp_strip_all_tags(get_the_content()), 34)); ?></p>

                    <dl class="quick-facts">
                        <?php foreach ($quick_keys as $key => $label) : ?>
                            <?php $spec = coc_spec($model_id, $key); ?>
                            <div class="quick-fact">
                                <dt><?php echo esc_html($label); ?></dt>
                                <dd><?php echo $spec['display'] ? esc_html($spec['display']) : esc_html__('Not confirmed', 'compareoxygen'); ?></dd>
                            </div>
                        <?php endforeach; ?>
                    </dl>

                    <div class="review-head__meta">
                        <span><?php echo compareoxygen_icon('check'); ?><?php printf(esc_html(_n('%d verified specification', '%d verified specifications', $verified, 'compareoxygen')), (int) $verified); ?></span>
                        <?php if ($last_verified) : ?>
                            <span><?php echo compareoxygen_icon('clock'); ?><?php printf(esc_html__('Source checked %s', 'compareoxygen'), esc_html(compareoxygen_format_date($last_verified))); ?></span>
                        <?php endif; ?>
                        <?php if ($model_number) : ?>
                            <span><?php echo compareoxygen_icon('file'); ?><?php printf(esc_html__('Model %s', 'compareoxygen'), esc_html($model_number)); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="button-row mt-3">
                        <a class="btn btn--primary btn--sm" href="<?php echo esc_url($compare_url); ?>"><?php echo compareoxygen_icon('scale'); ?><?php esc_html_e('Compare this model', 'compareoxygen'); ?></a>
                        <a class="btn btn--secondary btn--sm" href="#specifications"><?php esc_html_e('Jump to specifications', 'compareoxygen'); ?><?php echo compareoxygen_icon('arrow-right'); ?></a>
                    </div>
                </div>
            </div>
        </section>

        <nav class="review-nav" aria-label="<?php esc_attr_e('Review sections', 'compareoxygen'); ?>">
            <div class="container">
                <ul>
                    <li><a href="#overview"><?php esc_html_e('Overview', 'compareoxygen'); ?></a></li>
                    <li><a href="#specifications"><?php esc_html_e('Specifications', 'compareoxygen'); ?></a></li>
                    <li><a href="#verdict"><?php esc_html_e('Verdict', 'compareoxygen'); ?></a></li>
                    <?php if ($pros || $cons) : ?><li><a href="#strengths"><?php esc_html_e('Strengths', 'compareoxygen'); ?></a></li><?php endif; ?>
                    <li><a href="#sources"><?php esc_html_e('Sources', 'compareoxygen'); ?></a></li>
                </ul>
            </div>
        </nav>

        <div class="container guide-layout">
            <?php compareoxygen_toc($toc, coc_compare_tool_url(), __('Compare concentrators', 'compareoxygen')); ?>

            <div>
                <?php compareoxygen_disclosure(); ?>

                <article class="article" id="overview">
                    <?php if (trim($prepared['content']) !== '') : ?>
                        <?php echo wp_kses_post($prepared['content']); ?>
                    <?php else : ?>
                        <h2><?php esc_html_e('What this record covers', 'compareoxygen'); ?></h2>
                        <p><?php esc_html_e('This page collects the specifications the manufacturer publishes for this model, each with its source and the date our editor verified it. Where the manufacturer does not publish a figure, the table below says “Not confirmed” instead of estimating.', 'compareoxygen'); ?></p>
                    <?php endif; ?>

                    <?php if ($evidence_level) : ?>
                        <p class="article-meta"><?php echo compareoxygen_icon('file'); ?><span><?php echo esc_html($evidence_level); ?></span></p>
                    <?php endif; ?>
                </article>

                <section class="section--tight" id="specifications">
                    <header class="section-head section-head--split-top">
                        <p class="eyebrow"><?php esc_html_e('Specification record', 'compareoxygen'); ?></p>
                        <h2 class="section-title"><?php printf(esc_html__('%s specifications', 'compareoxygen'), esc_html(get_the_title())); ?></h2>
                        <p class="section-intro"><?php esc_html_e('Only facts with a manufacturer source and a verification date are published. Every row links back to the source we checked.', 'compareoxygen'); ?></p>
                    </header>
                    <?php get_template_part('template-parts/spec', 'table', ['model_id' => $model_id]); ?>
                </section>

                <section class="section--tight" id="verdict">
                    <?php
                    $flow = coc_spec($model_id, 'flow_type')['display'];
                    $weight = coc_spec($model_id, 'weight')['display'];
                    $battery_value = coc_spec($model_id, 'battery_extended')['display'] ?: coc_spec($model_id, 'battery_standard')['display'];
                    $verdict_bits = [];
                    if ($weight) { $verdict_bits[] = sprintf(__('a published weight of %s', 'compareoxygen'), $weight); }
                    if ($battery_value) { $verdict_bits[] = sprintf(__('a stated battery life of %s', 'compareoxygen'), $battery_value); }
                    if ($flow) { $verdict_bits[] = sprintf(__('%s oxygen delivery', 'compareoxygen'), strtolower($flow)); }
                    ?>
                    <div class="verdict-card">
                        <p class="eyebrow"><?php esc_html_e('Editorial verdict', 'compareoxygen'); ?></p>
                        <h2><?php echo $award ? esc_html($award) : esc_html__('What the verified record supports', 'compareoxygen'); ?></h2>
                        <p>
                            <?php
                            if ($verdict_bits) {
                                printf(
                                    esc_html__('Based on manufacturer documentation, this record documents %s. That is a description of published facts, not a clinical recommendation: whether this device suits a particular person depends on prescription, oxygen needs, and daily routine.', 'compareoxygen'),
                                    esc_html(implode(', ', $verdict_bits))
                                );
                            } else {
                                esc_html_e('This record does not yet contain enough verified specifications for an editorial assessment. We leave the assessment empty rather than infer performance from marketing language.', 'compareoxygen');
                            }
                            ?>
                        </p>
                        <?php if ($award) : ?>
                            <p class="muted small"><?php esc_html_e('The award above is an editorial decision based on the verified record. It is not a paid placement.', 'compareoxygen'); ?></p>
                        <?php endif; ?>

                        <div class="verdict-card__grid">
                            <div>
                                <h3><?php esc_html_e('Best documented for', 'compareoxygen'); ?></h3>
                                <p><?php echo $best_for ? esc_html($best_for) : esc_html__('Not stated. We do not assign a use case without evidence.', 'compareoxygen'); ?></p>
                            </div>
                            <div>
                                <h3><?php esc_html_e('What we cannot tell you', 'compareoxygen'); ?></h3>
                                <p><?php esc_html_e('We cannot say whether this concentrator meets your prescribed flow, whether your airline will accept it, or how it performs in your home. Those answers come from your clinician, oxygen supplier, and airline.', 'compareoxygen'); ?></p>
                            </div>
                        </div>
                    </div>
                </section>

                <?php if ($pros || $cons) : ?>
                    <section class="section--tight" id="strengths">
                        <header class="section-head section-head--split-top">
                            <p class="eyebrow"><?php esc_html_e('Strengths and limitations', 'compareoxygen'); ?></p>
                            <h2 class="section-title"><?php esc_html_e('What stands out in the published record', 'compareoxygen'); ?></h2>
                            <p class="section-intro"><?php esc_html_e('These notes describe documented specifications and trade-offs, not hands-on measurements. They are written by our editors, not supplied by the manufacturer.', 'compareoxygen'); ?></p>
                        </header>
                        <div class="pros-cons">
                            <div class="pros-cons__col pros-cons__col--pros">
                                <h3><?php echo compareoxygen_icon('check'); ?><?php esc_html_e('Documented strengths', 'compareoxygen'); ?></h3>
                                <ul>
                                    <?php foreach ($pros as $item) : ?><li><?php echo esc_html($item); ?></li><?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="pros-cons__col pros-cons__col--cons">
                                <h3><?php echo compareoxygen_icon('minus'); ?><?php esc_html_e('Limitations to weigh', 'compareoxygen'); ?></h3>
                                <ul>
                                    <?php foreach ($cons as $item) : ?><li><?php echo esc_html($item); ?></li><?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <section class="section--tight" id="sources">
                    <header class="section-head section-head--split-top">
                        <p class="eyebrow"><?php esc_html_e('Evidence', 'compareoxygen'); ?></p>
                        <h2 class="section-title"><?php esc_html_e('Sources and verification dates', 'compareoxygen'); ?></h2>
                        <p class="section-intro"><?php esc_html_e('Each source below was opened by our editor on the date shown. If a manufacturer changes a specification, the date tells you how current our record is.', 'compareoxygen'); ?></p>
                    </header>
                    <?php compareoxygen_sources($model_id); ?>
                </section>

                <div class="related-panel">
                    <h2><?php esc_html_e('Keep comparing', 'compareoxygen'); ?></h2>
                    <div class="related-links">
                        <a class="related-link" href="<?php echo esc_url($compare_url); ?>"><?php printf(esc_html__('Compare the %s with another model', 'compareoxygen'), esc_html(get_the_title())); ?><span><?php esc_html_e('Comparison tool', 'compareoxygen'); ?></span></a>
                        <?php
                        $others = coc_models(['numberposts' => 4, 'post__not_in' => [$model_id]]);
                        foreach ($others as $other) :
                            ?>
                            <a class="related-link" href="<?php echo esc_url(get_permalink($other)); ?>"><?php echo esc_html(get_the_title($other)); ?><span><?php echo esc_html(coc_brand_of($other->ID)); ?></span></a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php compareoxygen_medical_note(); ?>
            </div>
        </div>
    </main>
    <?php
endwhile;

get_footer();
