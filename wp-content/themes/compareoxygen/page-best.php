<?php
/**
 * Template Name: Best Of guide
 * Description: Editorial awards and the full reviewed shortlist.
 *
 * @package compareoxygen
 */

get_header();

$models = coc_models(['numberposts' => -1]);
$awards = coc_awards();
$use_cases = get_terms(['taxonomy' => 'poc_use_case', 'hide_empty' => true]);
$latest = '';
foreach ($models as $model) {
    $checked = coc_last_verified($model->ID);
    if ($checked > $latest) { $latest = $checked; }
}
$total_facts = 0;
foreach ($models as $model) { $total_facts += coc_verified_count($model->ID); }

$faqs = [
    __('Are these awards paid placements?', 'compareoxygen') => __('No. Awards are editorial decisions recorded in the site’s award settings. A brand cannot buy a label, and the disclosure explains the commercial relationship in full. The award rationale is published beside each selection.', 'compareoxygen'),
    __('What makes a model “best overall”?', 'compareoxygen') => __('Our editors weigh the verified record as a whole: how complete the documentation is, how the published weight and battery figures compare with peers, and whether the manufacturer clearly documents travel and warranty terms. We do not convert those judgements into a fake numeric score.', 'compareoxygen'),
    __('Why do some models have no award?', 'compareoxygen') => __('Awards are deliberately limited. A model can be fully reviewed and still not receive a label if another documented model is a better fit for that specific award. Reviewing a model does not entitle it to an award.', 'compareoxygen'),
    __('Is the cheapest model the best value?', 'compareoxygen') => __('We do not publish a value ranking. Manufacturer list prices are often unpublished or change frequently, and the cost of batteries, sieve-bed service, and warranty coverage differs. A low headline price can carry higher long-term costs.', 'compareoxygen'),
    __('Can you tell me which one my doctor would choose?', 'compareoxygen') => __('No. This is general research, not medical advice. Your prescription, flow needs, and travel plans belong in a conversation with your clinician and oxygen supplier. Use these pages to prepare specific questions for that conversation.', 'compareoxygen'),
];
?>
<main id="main-content">
    <?php compareoxygen_breadcrumbs(); ?>

    <section class="page-hero page-hero--mint">
        <div class="container page-hero__inner">
            <p class="eyebrow"><?php esc_html_e('Editorial awards', 'compareoxygen'); ?></p>
            <h1><?php esc_html_e('Best portable oxygen concentrators', 'compareoxygen'); ?></h1>
            <p class="page-hero__lede"><?php esc_html_e('Our editors assign a small number of labels after reviewing the verified record for each model. No score is invented, no ranking is sold, and every claim links back to a manufacturer source.', 'compareoxygen'); ?></p>
            <div class="page-hero__meta">
                <span><?php echo compareoxygen_icon('spark'); ?><?php echo esc_html(sprintf(_n('%s editorial award', '%s editorial awards', count($awards), 'compareoxygen'), number_format_i18n(count($awards)))); ?></span>
                <span><?php echo compareoxygen_icon('check'); ?><?php echo esc_html(sprintf(_n('%s model reviewed', '%s models reviewed', count($models), 'compareoxygen'), number_format_i18n(count($models)))); ?></span>
                <?php if ($latest) : ?>
                    <span><?php echo compareoxygen_icon('clock'); ?><?php printf(esc_html__('Latest source check %s', 'compareoxygen'), esc_html(compareoxygen_format_date($latest))); ?></span>
                <?php endif; ?>
            </div>
            <div class="button-row mt-3">
                <a class="btn btn--primary" href="<?php echo esc_url(coc_compare_tool_url()); ?>"><?php echo compareoxygen_icon('scale'); ?><?php esc_html_e('Compare the shortlist', 'compareoxygen'); ?></a>
                <a class="btn btn--secondary" href="#all-models"><?php esc_html_e('See every reviewed model', 'compareoxygen'); ?><?php echo compareoxygen_icon('arrow-right'); ?></a>
            </div>
        </div>
    </section>

    <div class="container">
        <?php compareoxygen_disclosure(); ?>
    </div>

    <section class="section section--tight" id="winners">
        <div class="container">
            <?php if ($awards) : ?>
                <div class="section-head">
                    <p class="eyebrow"><?php esc_html_e('The winners', 'compareoxygen'); ?></p>
                    <h2 class="section-title"><?php esc_html_e('What each award means', 'compareoxygen'); ?></h2>
                    <p class="section-intro"><?php esc_html_e('Each label answers a different question. Read the rationale, then check the specification record before treating it as a shortlist.', 'compareoxygen'); ?></p>
                </div>
                <div class="award-grid">
                    <?php foreach ($awards as $award) : ?>
                        <?php
                        $winner = $award['post'];
                        $weight = coc_spec($winner->ID, 'weight')['display'];
                        $battery = coc_spec($winner->ID, 'battery_extended')['display'] ?: coc_spec($winner->ID, 'battery_standard')['display'];
                        $settings = coc_spec($winner->ID, 'settings')['display'];
                        ?>
                        <article class="award-card">
                            <span class="award-card__label"><?php echo compareoxygen_icon('spark'); ?><?php echo esc_html($award['label']); ?></span>
                            <h3><a href="<?php echo esc_url(get_permalink($winner)); ?>"><?php echo esc_html(get_the_title($winner)); ?></a></h3>
                            <p class="award-card__brand"><?php echo esc_html(coc_brand_of($winner->ID)); ?></p>
                            <p><?php echo esc_html($award['why']); ?></p>
                            <div class="award-card__specs">
                                <div><?php esc_html_e('Weight', 'compareoxygen'); ?><strong><?php echo $weight ? esc_html($weight) : esc_html__('Not confirmed', 'compareoxygen'); ?></strong></div>
                                <div><?php esc_html_e('Battery', 'compareoxygen'); ?><strong><?php echo $battery ? esc_html($battery) : esc_html__('Not confirmed', 'compareoxygen'); ?></strong></div>
                                <div><?php esc_html_e('Settings', 'compareoxygen'); ?><strong><?php echo $settings ? esc_html($settings) : esc_html__('Not confirmed', 'compareoxygen'); ?></strong></div>
                            </div>
                            <div class="button-row mt-3">
                                <a class="btn btn--primary btn--sm" href="<?php echo esc_url(get_permalink($winner)); ?>"><?php esc_html_e('Read the review', 'compareoxygen'); ?></a>
                                <a class="btn btn--ghost btn--sm" href="<?php echo esc_url(add_query_arg(['first' => $winner->ID], coc_compare_tool_url())); ?>"><?php esc_html_e('Compare it', 'compareoxygen'); ?><?php echo compareoxygen_icon('arrow-right'); ?></a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="empty-state">
                    <h2><?php esc_html_e('Awards are being considered', 'compareoxygen'); ?></h2>
                    <p><?php esc_html_e('No editorial labels have been assigned yet. We publish awards only after the underlying specifications have been source-checked. The full model list below remains available in the meantime.', 'compareoxygen'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="section section--white">
        <div class="container">
            <div class="section-head">
                <p class="eyebrow"><?php esc_html_e('Method', 'compareoxygen'); ?></p>
                <h2 class="section-title"><?php esc_html_e('How an award is decided', 'compareoxygen'); ?></h2>
            </div>
            <div class="steps">
                <article class="step">
                    <h3><?php esc_html_e('Build the evidence record', 'compareoxygen'); ?></h3>
                    <p><?php esc_html_e('An editor collects the manufacturer’s product page, specification sheet, and manual, then records each fact with its source and verification date.', 'compareoxygen'); ?></p>
                </article>
                <article class="step">
                    <h3><?php esc_html_e('Compare like with like', 'compareoxygen'); ?></h3>
                    <p><?php esc_html_e('Published figures are compared only where the manufacturer states the test conditions. A battery claim without a stated setting is not treated as equivalent to one with it.', 'compareoxygen'); ?></p>
                </article>
                <article class="step">
                    <h3><?php esc_html_e('Assign a limited label', 'compareoxygen'); ?></h3>
                    <p><?php esc_html_e('Editors agree on a small set of labels and write the rationale. The labels are stored as editorial settings, so they can be revised when a record changes.', 'compareoxygen'); ?></p>
                </article>
            </div>
            <p class="muted small mt-3"><?php esc_html_e('We do not use a star rating, a composite score, or a hands-on “lab test” claim. No such testing programme exists for this site.', 'compareoxygen'); ?></p>
        </div>
    </section>

    <section class="section" id="all-models">
        <div class="container">
            <div class="section-head">
                <p class="eyebrow"><?php esc_html_e('The full shortlist', 'compareoxygen'); ?></p>
                <h2 class="section-title"><?php esc_html_e('Every model with a verified record', 'compareoxygen'); ?></h2>
                <p class="section-intro"><?php printf(esc_html__('%1$s published records and %2$s source-verified facts. Cards show only confirmed values.', 'compareoxygen'), esc_html(number_format_i18n(count($models))), esc_html(number_format_i18n($total_facts))); ?></p>
            </div>
            <?php if ($models) : ?>
                <div class="product-grid">
                    <?php foreach ($models as $model) : ?>
                        <?php compareoxygen_product_card($model->ID); ?>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="empty-state"><h2><?php esc_html_e('No models published yet', 'compareoxygen'); ?></h2><p><?php esc_html_e('Reviews appear once their sources have been verified.', 'compareoxygen'); ?></p></div>
            <?php endif; ?>
        </div>
    </section>

    <?php if (!empty($use_cases) && !is_wp_error($use_cases)) : ?>
        <section class="section section--white">
            <div class="container">
                <div class="section-head">
                    <p class="eyebrow"><?php esc_html_e('Narrow by situation', 'compareoxygen'); ?></p>
                    <h2 class="section-title"><?php esc_html_e('The same devices, different priorities', 'compareoxygen'); ?></h2>
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

    <section class="section">
        <div class="container container--reading">
            <div class="section-head">
                <p class="eyebrow"><?php esc_html_e('Awards FAQ', 'compareoxygen'); ?></p>
                <h2 class="section-title"><?php esc_html_e('How to read an award label', 'compareoxygen'); ?></h2>
            </div>
            <?php compareoxygen_faq($faqs); ?>
            <?php compareoxygen_faq_schema($faqs); ?>
            <?php compareoxygen_medical_note(); ?>
        </div>
    </section>

    <div class="container">
        <?php
        compareoxygen_cta_band(
            __('Shortlist two models and read the raw record', 'compareoxygen'),
            __('Awards are a starting point. The comparison table shows exactly what each manufacturer publishes — and what neither of them does.', 'compareoxygen'),
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
