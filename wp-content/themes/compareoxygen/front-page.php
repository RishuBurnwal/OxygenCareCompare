<?php
/**
 * Homepage.
 *
 * @package compareoxygen
 */

get_header();

$models = coc_models(['numberposts' => 6]);
$hero_model = $models[0] ?? null;
$awards = coc_awards();
$use_cases = get_terms(['taxonomy' => 'poc_use_case', 'hide_empty' => true]);
$guides = get_posts(['post_type' => 'post', 'post_status' => 'publish', 'numberposts' => 3]);
$total_facts = 0;
foreach ($models as $model_item) { $total_facts += coc_verified_count($model_item->ID); }
$brand_count = wp_count_terms(['taxonomy' => 'poc_brand', 'hide_empty' => true]);
$brand_count = is_wp_error($brand_count) ? 0 : (int) $brand_count;

$faqs = [];
for ($faq_index = 1; $faq_index <= 5; $faq_index++) {
    $faq_question = coc_opt('faq_' . $faq_index . '_q');
    if ($faq_question !== '') {
        $faqs[$faq_question] = coc_opt('faq_' . $faq_index . '_a');
    }
}
?>
<main id="main-content">
    <section class="hero">
        <div class="container hero__inner">
            <div class="hero__copy">
                <p class="eyebrow"><?php echo esc_html(coc_opt('hero_eyebrow')); ?></p>
                <h1><?php echo esc_html(coc_opt('hero_title')); ?><span class="accent">.</span></h1>
                <p class="hero__lede"><?php echo esc_html(coc_opt('hero_lede')); ?></p>
                <div class="button-row hero__actions">
                    <a class="btn btn--primary btn--lg" href="<?php echo esc_url(coc_opt('hero_cta1_url') ?: coc_compare_tool_url()); ?>"><?php echo compareoxygen_icon('scale'); ?><?php echo esc_html(coc_opt('hero_cta1_label')); ?></a>
                    <a class="btn btn--secondary btn--lg" href="<?php echo esc_url(coc_opt('hero_cta2_url')); ?>"><?php echo esc_html(coc_opt('hero_cta2_label')); ?><?php echo compareoxygen_icon('arrow-right'); ?></a>
                </div>
                <div class="hero__meta">
                    <span><?php echo compareoxygen_icon('check'); ?><?php echo esc_html(coc_opt('hero_meta_1')); ?></span>
                    <span><?php echo compareoxygen_icon('shield'); ?><?php echo esc_html(coc_opt('hero_meta_2')); ?></span>
                </div>
            </div>

            <div class="hero-visual">
                <div class="hero-card">
                    <div class="hero-card__top">
                        <span class="hero-card__label"><?php esc_html_e('Specification record', 'compareoxygen'); ?></span>
                        <span class="badge badge--success"><?php echo compareoxygen_icon('check'); ?><?php esc_html_e('Source checked', 'compareoxygen'); ?></span>
                    </div>
                    <div class="hero-card__stage">
                        <?php compareoxygen_device_illustration('compact', 'teal'); ?>
                    </div>
                    <p class="hero-card__caption">
                        <span>
                            <strong><?php echo $hero_model ? esc_html(get_the_title($hero_model)) : esc_html__('Portable concentrator', 'compareoxygen'); ?></strong>
                            <?php esc_html_e('Generic technical illustration — not a manufacturer photograph', 'compareoxygen'); ?>
                        </span>
                        <?php if ($hero_model) : ?>
                            <a class="link-arrow" href="<?php echo esc_url(get_permalink($hero_model)); ?>"><?php esc_html_e('View record', 'compareoxygen'); ?><?php echo compareoxygen_icon('arrow-right'); ?></a>
                        <?php endif; ?>
                    </p>
                </div>

                <?php if ($hero_model) : ?>
                    <?php
                    $chip_specs = [
                        ['weight', __('Weight', 'compareoxygen'), 'weight', 'spec-chip--1'],
                        ['battery_extended', __('Battery, extended', 'compareoxygen'), 'battery', 'spec-chip--2'],
                        ['settings', __('Flow settings', 'compareoxygen'), 'layers', 'spec-chip--3'],
                    ];
                    foreach ($chip_specs as $chip) :
                        $spec = coc_spec($hero_model->ID, $chip[0]);
                        if (!$spec['display']) { continue; }
                        ?>
                        <div class="spec-chip <?php echo esc_attr($chip[3]); ?>">
                            <?php echo compareoxygen_icon($chip[2]); ?>
                            <span><?php echo esc_html($chip[1]); ?><strong><?php echo esc_html($spec['display']); ?></strong></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="trust-strip" aria-label="<?php esc_attr_e('Research standards', 'compareoxygen'); ?>">
        <div class="container trust-strip__inner">
            <span class="trust-strip__item"><?php echo compareoxygen_icon('check'); ?><strong><?php echo esc_html(coc_opt('trust_1')); ?></strong></span>
            <span class="trust-strip__item"><?php echo compareoxygen_icon('clock'); ?><?php echo esc_html(coc_opt('trust_2')); ?></span>
            <span class="trust-strip__item"><?php echo compareoxygen_icon('shield'); ?><?php echo esc_html(coc_opt('trust_3')); ?></span>
            <span class="trust-strip__item"><?php echo compareoxygen_icon('plane'); ?><?php echo esc_html(coc_opt('trust_4')); ?></span>
        </div>
    </section>

    <div class="compare-band">
        <div class="container">
            <?php get_template_part('template-parts/compare', 'widget', ['models' => $models, 'compact' => true]); ?>
        </div>
    </div>

    <section class="section" id="top-picks">
        <div class="container">
            <div class="section-head">
                <p class="eyebrow"><?php echo esc_html(coc_opt('picks_eyebrow')); ?></p>
                <h2 class="section-title"><?php echo esc_html(coc_opt('picks_title')); ?></h2>
                <p class="section-intro"><?php echo esc_html(coc_opt('picks_intro')); ?></p>
            </div>
            <?php if ($models) : ?>
                <div class="product-grid">
                    <?php foreach (array_slice($models, 0, 3) as $model) : ?>
                        <?php compareoxygen_product_card($model->ID); ?>
                    <?php endforeach; ?>
                </div>
                <div class="button-row mt-3">
                    <a class="btn btn--secondary" href="<?php echo esc_url(get_post_type_archive_link('poc_model') ?: home_url('/reviews/')); ?>"><?php esc_html_e('Browse every review', 'compareoxygen'); ?><?php echo compareoxygen_icon('arrow-right'); ?></a>
                    <a class="btn btn--ghost" href="<?php echo esc_url(compareoxygen_page_url('best', '/best/portable-oxygen-concentrators/')); ?>"><?php esc_html_e('See the Best Of guide', 'compareoxygen'); ?><?php echo compareoxygen_icon('arrow-right'); ?></a>
                </div>
            <?php else : ?>
                <div class="empty-state"><h2><?php esc_html_e('Model records are being verified', 'compareoxygen'); ?></h2><p><?php esc_html_e('Reviews appear here once their manufacturer sources have been checked. In the meantime, our methodology explains exactly what we publish.', 'compareoxygen'); ?></p></div>
            <?php endif; ?>
        </div>
    </section>

    <?php if (!empty($use_cases) && !is_wp_error($use_cases)) : ?>
        <section class="section section--white">
            <div class="container">
                <div class="section-head">
                    <p class="eyebrow"><?php echo esc_html(coc_opt('cases_eyebrow')); ?></p>
                    <h2 class="section-title"><?php echo esc_html(coc_opt('cases_title')); ?></h2>
                    <p class="section-intro"><?php echo esc_html(coc_opt('cases_intro')); ?></p>
                </div>
                <div class="use-case-grid">
                    <?php
                    $case_icons = ['travel' => 'plane', 'daily-use' => 'route', 'lightweight' => 'weight', 'backup' => 'battery'];
                    foreach ($use_cases as $term) :
                        $icon = $case_icons[$term->slug] ?? 'route';
                        ?>
                        <a class="use-case" href="<?php echo esc_url(get_term_link($term)); ?>">
                            <span class="use-case__icon"><?php echo compareoxygen_icon($icon); ?></span>
                            <span>
                                <h3><?php echo esc_html($term->name); ?></h3>
                                <p><?php echo esc_html($term->description ?: __('Compare models documented for this context.', 'compareoxygen')); ?></p>
                            </span>
                            <span class="use-case__arrow"><?php echo compareoxygen_icon('arrow-up-right'); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($awards) : ?>
        <section class="section section--mint" id="awards">
            <div class="container">
                <div class="section-head">
                    <p class="eyebrow"><?php echo esc_html(coc_opt('awards_eyebrow')); ?></p>
                    <h2 class="section-title"><?php echo esc_html(coc_opt('awards_title')); ?></h2>
                    <p class="section-intro"><?php echo esc_html(coc_opt('awards_intro')); ?></p>
                </div>
                <div class="award-grid">
                    <?php foreach (array_slice($awards, 0, 3) as $award) : ?>
                        <?php $award_post = $award['post']; ?>
                        <article class="award-card">
                            <span class="award-card__label"><?php echo compareoxygen_icon('spark'); ?><?php echo esc_html($award['label']); ?></span>
                            <h3><a href="<?php echo esc_url(get_permalink($award_post)); ?>"><?php echo esc_html(get_the_title($award_post)); ?></a></h3>
                            <p class="award-card__brand"><?php echo esc_html(coc_brand_of($award_post->ID)); ?></p>
                            <p><?php echo esc_html($award['why']); ?></p>
                            <div class="award-card__specs">
                                <?php foreach (['weight' => __('Weight', 'compareoxygen'), 'battery_extended' => __('Battery', 'compareoxygen')] as $key => $label) : ?>
                                    <?php $spec = coc_spec($award_post->ID, $key); ?>
                                    <div><?php echo esc_html($label); ?><strong><?php echo $spec['display'] ? esc_html($spec['display']) : esc_html__('Not confirmed', 'compareoxygen'); ?></strong></div>
                                <?php endforeach; ?>
                            </div>
                            <div class="button-row mt-3">
                                <a class="btn btn--primary btn--sm" href="<?php echo esc_url(get_permalink($award_post)); ?>"><?php esc_html_e('Read the review', 'compareoxygen'); ?></a>
                                <a class="btn btn--ghost btn--sm" href="<?php echo esc_url(compareoxygen_page_url('best', '/best/portable-oxygen-concentrators/')); ?>"><?php esc_html_e('Why it won', 'compareoxygen'); ?><?php echo compareoxygen_icon('arrow-right'); ?></a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="section">
        <div class="container">
            <div class="section-head">
                <p class="eyebrow"><?php echo esc_html(coc_opt('steps_eyebrow')); ?></p>
                <h2 class="section-title"><?php echo esc_html(coc_opt('steps_title')); ?></h2>
                <p class="section-intro"><?php echo esc_html(coc_opt('steps_intro')); ?></p>
            </div>
            <div class="steps">
                <article class="step">
                    <h3><?php echo esc_html(coc_opt('step1_title')); ?></h3>
                    <p><?php echo esc_html(coc_opt('step1_body')); ?></p>
                </article>
                <article class="step">
                    <h3><?php echo esc_html(coc_opt('step2_title')); ?></h3>
                    <p><?php echo esc_html(coc_opt('step2_body')); ?></p>
                </article>
                <article class="step">
                    <h3><?php echo esc_html(coc_opt('step3_title')); ?></h3>
                    <p><?php echo esc_html(coc_opt('step3_body')); ?></p>
                </article>
            </div>
        </div>
    </section>

    <section class="section section--white">
        <div class="container">
            <div class="section-head">
                <p class="eyebrow"><?php echo esc_html(coc_opt('features_eyebrow')); ?></p>
                <h2 class="section-title"><?php echo esc_html(coc_opt('features_title')); ?></h2>
                <p class="section-intro"><?php echo esc_html(coc_opt('features_intro')); ?></p>
            </div>
            <div class="feature-grid">
                <article class="feature-cell">
                    <span class="feature-cell__icon"><?php echo compareoxygen_icon('layers'); ?></span>
                    <h3><?php esc_html_e('Pulse dose and continuous flow', 'compareoxygen'); ?></h3>
                    <p><?php esc_html_e('Pulse-dose devices release oxygen as you breathe; continuous-flow devices release a steady stream. They are not interchangeable on a prescription.', 'compareoxygen'); ?></p>
                </article>
                <article class="feature-cell">
                    <span class="feature-cell__icon"><?php echo compareoxygen_icon('battery'); ?></span>
                    <h3><?php esc_html_e('Battery life at setting one', 'compareoxygen'); ?></h3>
                    <p><?php esc_html_e('Published runtimes usually describe the lowest setting. A 13-hour claim will not hold at the highest setting, so compare equal settings.', 'compareoxygen'); ?></p>
                </article>
                <article class="feature-cell">
                    <span class="feature-cell__icon"><?php echo compareoxygen_icon('weight'); ?></span>
                    <h3><?php esc_html_e('Weight with the battery fitted', 'compareoxygen'); ?></h3>
                    <p><?php esc_html_e('We list weight with the battery configuration the manufacturer states. Extended batteries add weight and change the carrying experience.', 'compareoxygen'); ?></p>
                </article>
                <article class="feature-cell">
                    <span class="feature-cell__icon"><?php echo compareoxygen_icon('volume'); ?></span>
                    <h3><?php esc_html_e('Noise in decibels (dBA)', 'compareoxygen'); ?></h3>
                    <p><?php esc_html_e('Decibel figures are usually measured at a specific setting. Without that setting, a number is hard to compare fairly — we include the context when published.', 'compareoxygen'); ?></p>
                </article>
                <article class="feature-cell">
                    <span class="feature-cell__icon"><?php echo compareoxygen_icon('plane'); ?></span>
                    <h3><?php esc_html_e('FAA acceptance for air travel', 'compareoxygen'); ?></h3>
                    <p><?php esc_html_e('A manufacturer statement of FAA compliance is a starting point. Airline approval, battery limits, and documentation remain separate checks before you fly.', 'compareoxygen'); ?></p>
                </article>
                <article class="feature-cell">
                    <span class="feature-cell__icon"><?php echo compareoxygen_icon('shield'); ?></span>
                    <h3><?php esc_html_e('Warranty and sieve beds', 'compareoxygen'); ?></h3>
                    <p><?php esc_html_e('A device warranty may or may not include sieve beds, which are a wear item. We record exactly what the manufacturer covers when it is documented.', 'compareoxygen'); ?></p>
                </article>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-head">
                <p class="eyebrow"><?php echo esc_html(coc_opt('reviews_eyebrow')); ?></p>
                <h2 class="section-title"><?php echo esc_html(coc_opt('reviews_title')); ?></h2>
            </div>
            <?php if ($models) : ?>
                <div class="review-list">
                    <?php foreach (array_slice($models, 0, 4) as $model) : ?>
                        <?php compareoxygen_review_row($model->ID); ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if ($guides) : ?>
        <section class="section section--white">
            <div class="container">
                <div class="section-head">
                    <p class="eyebrow"><?php echo esc_html(coc_opt('guides_eyebrow')); ?></p>
                    <h2 class="section-title"><?php echo esc_html(coc_opt('guides_title')); ?></h2>
                </div>
                <div class="guide-grid">
                    <?php foreach ($guides as $guide) : ?>
                        <?php
                        $words = str_word_count(wp_strip_all_tags($guide->post_content));
                        $category = get_the_category($guide->ID);
                        ?>
                        <article class="guide-card">
                            <div class="guide-card__visual"><?php echo compareoxygen_icon('book'); ?></div>
                            <div class="guide-card__body">
                                <p class="guide-card__cat"><?php echo esc_html($category ? $category[0]->name : __('Guide', 'compareoxygen')); ?></p>
                                <h3><a href="<?php echo esc_url(get_permalink($guide)); ?>"><?php echo esc_html(get_the_title($guide)); ?></a></h3>
                                <p><?php echo esc_html(wp_trim_words($guide->post_excerpt ?: wp_strip_all_tags($guide->post_content), 20)); ?></p>
                                <div class="guide-card__meta">
                                    <span><?php printf(esc_html__('%d min read', 'compareoxygen'), compareoxygen_reading_time($guide->post_content)); ?></span>
                                    <span><?php echo esc_html(get_the_date('M j, Y', $guide)); ?></span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                <div class="button-row mt-3">
                    <a class="btn btn--secondary" href="<?php echo esc_url(compareoxygen_page_url('guides')); ?>"><?php esc_html_e('All buying guides', 'compareoxygen'); ?><?php echo compareoxygen_icon('arrow-right'); ?></a>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="section">
        <div class="container">
            <div class="trust-panel">
                <div>
                    <p class="eyebrow eyebrow--light"><?php echo esc_html(coc_opt('method_eyebrow')); ?></p>
                    <h2><?php echo esc_html(coc_opt('method_title')); ?></h2>
                    <p><?php echo esc_html(coc_opt('method_body')); ?></p>
                    <div class="button-row mt-3">
                        <a class="btn btn--onDark btn--sm" href="<?php echo esc_url(compareoxygen_page_url('how-we-rate')); ?>"><?php esc_html_e('Read how we rate', 'compareoxygen'); ?><?php echo compareoxygen_icon('arrow-right'); ?></a>
                        <a class="btn btn--ghostLight btn--sm" href="<?php echo esc_url(compareoxygen_page_url('editorial-policy')); ?>"><?php esc_html_e('Editorial policy', 'compareoxygen'); ?></a>
                    </div>
                </div>
                <div class="trust-panel__stats">
                    <div class="stat-card"><strong class="num"><?php echo esc_html(number_format_i18n(count($models))); ?></strong><span><?php esc_html_e('published model records', 'compareoxygen'); ?></span></div>
                    <div class="stat-card"><strong class="num"><?php echo esc_html(number_format_i18n($total_facts)); ?></strong><span><?php esc_html_e('source-verified facts', 'compareoxygen'); ?></span></div>
                    <div class="stat-card"><strong class="num"><?php echo esc_html(number_format_i18n($brand_count)); ?></strong><span><?php esc_html_e('brands documented', 'compareoxygen'); ?></span></div>
                    <div class="stat-card"><strong class="num">0</strong><span><?php esc_html_e('paid ranking positions', 'compareoxygen'); ?></span></div>
                </div>
            </div>
        </div>
    </section>

    <section class="section section--white">
        <div class="container container--reading">
            <div class="section-head">
                <p class="eyebrow"><?php echo esc_html(coc_opt('faq_eyebrow')); ?></p>
                <h2 class="section-title"><?php echo esc_html(coc_opt('faq_title')); ?></h2>
            </div>
            <?php compareoxygen_faq($faqs); ?>
            <?php compareoxygen_faq_schema($faqs); ?>
            <div class="mt-3">
                <a class="link-arrow" href="<?php echo esc_url(compareoxygen_page_url('contact')); ?>"><?php esc_html_e('Ask a different question', 'compareoxygen'); ?><?php echo compareoxygen_icon('arrow-right'); ?></a>
            </div>
        </div>
    </section>

    <div class="container">
        <?php
        compareoxygen_cta_band(
            __('Ready to put two models side by side?', 'compareoxygen'),
            __('The comparison tool shows what each manufacturer publishes, where the two devices differ, and what remains unconfirmed.', 'compareoxygen'),
            __('Compare concentrators', 'compareoxygen'),
            coc_compare_tool_url(),
            __('Read the Best Of guide', 'compareoxygen'),
            compareoxygen_page_url('best', '/best/portable-oxygen-concentrators/')
        );
        ?>
    </div>
</main>
<?php
get_footer();
