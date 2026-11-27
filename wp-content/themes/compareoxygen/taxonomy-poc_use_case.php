<?php
/**
 * Use-case archive.
 *
 * @package compareoxygen
 */

get_header();

$term = get_queried_object();
if (!$term instanceof WP_Term) { get_footer(); return; }

$models = coc_models([
    'numberposts' => -1,
    'tax_query' => [['taxonomy' => 'poc_use_case', 'field' => 'term_id', 'terms' => $term->term_id]],
]);
$total_facts = 0;
foreach ($models as $model) { $total_facts += coc_verified_count($model->ID); }

$tips = [
    'travel' => [
        [__('Check the FAA statement, not the badge', 'compareoxygen'), __('A manufacturer statement of FAA compliance is a starting point. Airline approval, battery count limits, and documentation are separate checks before you fly.', 'compareoxygen')],
        [__('Compare battery figures at the same setting', 'compareoxygen'), __('A published runtime usually describes the lowest setting. If the manufacturer states the test setting, use it; if not, treat the number as approximate.', 'compareoxygen')],
        [__('Plan for charging, not just flying', 'compareoxygen'), __('Recharge time and available power at your destination matter as much as runtime. Check whether AC and DC charging are both supported.', 'compareoxygen')],
    ],
    'daily-use' => [
        [__('Weight with the battery fitted', 'compareoxygen'), __('The carrying weight that matters includes the battery configuration you will actually use. Extended batteries add weight and change balance.', 'compareoxygen')],
        [__('Noise at the setting you use', 'compareoxygen'), __('Decibel figures are only comparable when measured at the same setting. A quiet number at setting one may not describe the sound at a higher setting.', 'compareoxygen')],
        [__('A charging routine you can keep', 'compareoxygen'), __('Daily use depends on a repeatable charging habit. Check charge time, spare-battery availability, and whether the battery is user-replaceable.', 'compareoxygen')],
    ],
    'lightweight' => [
        [__('Separate device weight from carry weight', 'compareoxygen'), __('Manufacturers sometimes quote the device alone. Check the weight with the battery fitted and whether a carry bag is included.', 'compareoxygen')],
        [__('Balance matters as much as grams', 'compareoxygen'), __('A shoulder bag and an extended battery change how a device carries. Published dimensions help you judge the shape, but try the configuration if you can.', 'compareoxygen')],
        [__('Do not trade away the prescription', 'compareoxygen'), __('A lighter device is not useful if it does not meet the prescribed flow mode and setting. Weight is a tie-breaker, not the first filter.', 'compareoxygen')],
    ],
    'backup' => [
        [__('Battery availability is part of the spec', 'compareoxygen'), __('A backup plan depends on spare batteries and charging options. Check whether batteries are user-replaceable and how long a recharge takes.', 'compareoxygen')],
        [__('Runtime claims need a setting', 'compareoxygen'), __('An extended battery figure is only meaningful with the setting it was measured at. Compare like with like, and plan a margin.', 'compareoxygen')],
        [__('A backup device is a clinical decision', 'compareoxygen'), __('Whether you need a backup concentrator, and which one, is a conversation with your clinician and supplier. This page is research, not a prescription.', 'compareoxygen')],
    ],
];
$use_tips = $tips[$term->slug] ?? [
    [__('Start with the prescribed mode', 'compareoxygen'), __('Pulse dose and continuous flow are not interchangeable. Establish the prescribed mode and setting before comparing devices.', 'compareoxygen')],
    [__('Compare battery figures at equal settings', 'compareoxygen'), __('Published runtimes usually describe the lowest setting. Use the stated test condition where the manufacturer provides one.', 'compareoxygen')],
    [__('Read the warranty details', 'compareoxygen'), __('Check what the manufacturer covers and for how long, including whether sieve beds are included. Wear items can dominate ownership cost.', 'compareoxygen')],
];
?>
<main id="main-content">
    <?php compareoxygen_breadcrumbs(); ?>

    <section class="page-hero page-hero--mint">
        <div class="container page-hero__inner">
            <p class="eyebrow"><?php esc_html_e('Comparison category', 'compareoxygen'); ?></p>
            <h1><?php printf(esc_html__('Portable oxygen concentrators for %s', 'compareoxygen'), esc_html(strtolower($term->name))); ?></h1>
            <p class="page-hero__lede"><?php echo esc_html($term->description ?: sprintf(__('Models whose verified specifications are relevant to %s. The category is an editorial organising tool, not a clinical recommendation.', 'compareoxygen'), strtolower($term->name))); ?></p>
            <div class="page-hero__meta">
                <span><?php echo compareoxygen_icon('layers'); ?><?php echo esc_html(sprintf(_n('%s documented model', '%s documented models', count($models), 'compareoxygen'), number_format_i18n(count($models)))); ?></span>
                <span><?php echo compareoxygen_icon('check'); ?><?php echo esc_html(sprintf(_n('%s verified fact', '%s verified facts', $total_facts, 'compareoxygen'), number_format_i18n($total_facts))); ?></span>
            </div>
        </div>
    </section>

    <div class="container">
        <?php compareoxygen_disclosure(); ?>
    </div>

    <section class="section section--tight">
        <div class="container">
            <?php if ($models) : ?>
                <div class="product-grid">
                    <?php foreach ($models as $model) : ?>
                        <?php compareoxygen_product_card($model->ID); ?>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="empty-state">
                    <h2><?php esc_html_e('No models assigned to this category yet', 'compareoxygen'); ?></h2>
                    <p><?php esc_html_e('Categories fill as reviewed models are assigned by an editor. We do not pad them with unreviewed products.', 'compareoxygen'); ?></p>
                    <p><a class="btn btn--primary" href="<?php echo esc_url(get_post_type_archive_link('poc_model') ?: home_url('/reviews/')); ?>"><?php esc_html_e('Browse all reviewed models', 'compareoxygen'); ?></a></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="section section--white">
        <div class="container">
            <div class="section-head">
                <p class="eyebrow"><?php esc_html_e('What to check', 'compareoxygen'); ?></p>
                <h2 class="section-title"><?php printf(esc_html__('Three filters for %s', 'compareoxygen'), esc_html(strtolower($term->name))); ?></h2>
            </div>
            <div class="steps">
                <?php foreach ($use_tips as $tip) : ?>
                    <article class="step">
                        <h3><?php echo esc_html($tip[0]); ?></h3>
                        <p><?php echo esc_html($tip[1]); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
            <div class="compare-note mt-3">
                <?php echo compareoxygen_icon('alert'); ?>
                <p><?php esc_html_e('This category does not assert that every model shown is clinically suitable for that situation. It groups records by the specification questions the situation raises. Confirm suitability with your clinician and oxygen supplier.', 'compareoxygen'); ?></p>
            </div>
        </div>
    </section>

    <div class="container">
        <?php
        compareoxygen_cta_band(
            __('Narrow it to two', 'compareoxygen'),
            __('Choose two models from this category and read the published differences, including the fields one manufacturer leaves blank.', 'compareoxygen'),
            __('Compare concentrators', 'compareoxygen'),
            coc_compare_tool_url(),
            __('All comparison categories', 'compareoxygen'),
            compareoxygen_page_url('compare')
        );
        ?>
    </div>
</main>
<?php
get_footer();
