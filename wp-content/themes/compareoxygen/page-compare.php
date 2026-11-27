<?php
/**
 * Template Name: Compare tool
 * Description: Two-model comparison tool with evidence table.
 *
 * @package compareoxygen
 */

get_header();

$ids = function_exists('coc_compare_ids') ? coc_compare_ids() : [];
$ids = array_values(array_filter(array_map('absint', (array) $ids)));
$first = $ids[0] ?? 0;
$second = $ids[1] ?? 0;

$models = coc_models(['numberposts' => -1]);
$has_pair = count($ids) >= 2;
$first_post = $has_pair ? get_post($first) : null;
$second_post = $has_pair ? get_post($second) : null;
$model_posts = [];
if ($has_pair) {
    foreach ($ids as $model_id) {
        $model_post = get_post($model_id);
        if ($model_post instanceof WP_Post) { $model_posts[] = $model_post; }
    }
}
$pair_differences = $has_pair ? COC_Core::difference_count_for($ids) : 0;
$pair_verified = $has_pair ? COC_Core::min_verified_for($ids) : '';
$model_titles = array_map(static function ($model_post) { return $model_post->post_title; }, $model_posts);
$name_list = '';
if ($model_titles) {
    $last = array_pop($model_titles);
    $name_list = $model_titles ? implode(', ', $model_titles) . ' and ' . $last : $last;
}
$comparisons = get_posts([
    'post_type' => 'poc_comparison',
    'post_status' => 'publish',
    'numberposts' => 4,
]);

$faqs = [
    __('Does the comparison tool recommend a winner?', 'compareoxygen') => __('No. It prints the specifications each manufacturer publishes, grouped for reading, and highlights where the two records differ. It does not score, rank, or declare a winner. A difference in battery life or weight is not automatically an advantage for a particular person.', 'compareoxygen'),
    __('Why are some cells marked “Not confirmed”?', 'compareoxygen') => __('A cell is confirmed only when we hold three things: a published value, the manufacturer URL it came from, and the date our editor checked it. If any of those is missing, the public site shows “Not confirmed” rather than a guess or a number copied from an unsourced list.', 'compareoxygen'),
    __('Can I trust the numbers on this page?', 'compareoxygen') => __('Every confirmed number links to its source and shows a verification date, so you can check it yourself. Manufacturers do change specifications without notice, so confirm any figure that affects your prescription, travel, or purchase directly with the manufacturer or your supplier.', 'compareoxygen'),
    __('Is the comparison tool free?', 'compareoxygen') => __('Yes. The tool is free to use and does not require an account. The site is funded by Oxygen Care Supply, a durable medical equipment supplier, and may earn revenue from product links. That relationship does not determine which specifications appear or how they are ordered.', 'compareoxygen'),
    __('Can I share a comparison?', 'compareoxygen') => __('Yes. Every comparison has its own clean URL in the form /compare/model-one-vs-model-two/, so you can bookmark it, refresh it, or send it to someone else. Add up to two more models with further -vs- segments and the same link keeps working. Approved editorial comparisons add context but are not required to share a pair.', 'compareoxygen'),
    __('Why can’t I compare the same model with itself?', 'compareoxygen') => __('A comparison requires two records. The tool rejects identical selections so that the table always shows a genuine side-by-side rather than a duplicated column.', 'compareoxygen'),
];
?>
<main id="main-content">
    <?php compareoxygen_breadcrumbs(); ?>

    <?php if ($has_pair && $first_post instanceof WP_Post && $second_post instanceof WP_Post) : ?>
        <section class="compare-page-head">
            <div class="container container--wide">
                <p class="eyebrow"><?php esc_html_e('Side-by-side comparison', 'compareoxygen'); ?></p>
                <h1 class="compare-page-title">
                    <?php foreach ($model_posts as $index => $model_post) : ?>
                        <?php if ($index > 0) : ?><span class="compare-page-title__vs" aria-hidden="true"><?php esc_html_e('VS', 'compareoxygen'); ?></span><?php endif; ?>
                        <span><?php echo esc_html($model_post->post_title); ?></span>
                    <?php endforeach; ?>
                </h1>
                <p class="compare-page-head__lede"><?php printf(esc_html__('Compare the published specifications of the %s. Every confirmed value carries its manufacturer source and verification date; gaps stay visible.', 'compareoxygen'), esc_html($name_list)); ?></p>
                <div class="compare-page-head__meta">
                    <span><?php echo compareoxygen_icon('list'); ?><?php printf(esc_html(_n('%s difference in published specifications', '%s differences in published specifications', $pair_differences, 'compareoxygen')), esc_html(number_format_i18n($pair_differences))); ?></span>
                    <?php if ($pair_verified) : ?>
                        <span><?php echo compareoxygen_icon('check'); ?><?php printf(esc_html__('Oldest source checked %s', 'compareoxygen'), esc_html(compareoxygen_format_date($pair_verified))); ?></span>
                    <?php endif; ?>
                    <span><?php echo compareoxygen_icon('shield'); ?><?php esc_html_e('No ranking implied by order', 'compareoxygen'); ?></span>
                </div>
                <div class="button-row mt-3">
                    <a class="btn btn--ghost btn--sm" href="<?php echo esc_url(coc_compare_tool_url()); ?>"><?php echo compareoxygen_icon('scale'); ?><?php esc_html_e('Change models', 'compareoxygen'); ?></a>
                    <button class="btn btn--secondary btn--sm" type="button" data-share="<?php echo esc_url(coc_compare_url($ids)); ?>"><?php echo compareoxygen_icon('arrow-up-right'); ?><span data-share-label><?php esc_html_e('Copy comparison link', 'compareoxygen'); ?></span></button>
                </div>
            </div>
        </section>

        <section class="section section--tight compare-pair-section">
            <div class="container container--wide">
                <?php get_template_part('template-parts/compare', 'results', ['models' => $ids, 'standalone' => true]); ?>
            </div>
        </section>
    <?php else : ?>
        <section class="page-hero page-hero--mint">
            <div class="container page-hero__inner">
                <p class="eyebrow"><?php esc_html_e('Comparison tool', 'compareoxygen'); ?></p>
                <h1><?php esc_html_e('Compare portable oxygen concentrators', 'compareoxygen'); ?></h1>
                <p class="page-hero__lede"><?php esc_html_e('Choose two published models to see every specification side by side, with its manufacturer source and verification date. After the table opens you can add up to four models to the same comparison.', 'compareoxygen'); ?></p>
                <div class="page-hero__meta">
                    <span><?php echo compareoxygen_icon('scale'); ?><?php echo esc_html(sprintf(_n('%s model available', '%s models available', count($models), 'compareoxygen'), number_format_i18n(count($models)))); ?></span>
                    <span><?php echo compareoxygen_icon('check'); ?><?php esc_html_e('Sources on every confirmed fact', 'compareoxygen'); ?></span>
                    <span><?php echo compareoxygen_icon('shield'); ?><?php esc_html_e('No paid placement', 'compareoxygen'); ?></span>
                </div>
            </div>
        </section>

        <section class="section section--tight">
            <div class="container">
                <?php if (count($models) < 2) : ?>
                    <div class="empty-state">
                        <h2><?php esc_html_e('Two published records are needed', 'compareoxygen'); ?></h2>
                        <p><?php esc_html_e('The comparison tool activates once at least two models have been reviewed and published with source-checked specifications.', 'compareoxygen'); ?></p>
                    </div>
                <?php else : ?>
                    <?php get_template_part('template-parts/compare', 'widget', ['models' => $models, 'first' => $first, 'second' => $second]); ?>
                    <div class="notice mt-3">
                        <p><strong><?php esc_html_e('Choose two models to build the table.', 'compareoxygen'); ?></strong> <?php esc_html_e('Start with the first field above. Suggestions are grouped by brand and show only verified weight values. After you choose the first model, it is removed from the second list so a duplicate is impossible.', 'compareoxygen'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <section class="section section--white">
        <div class="container">
            <div class="section-head">
                <p class="eyebrow"><?php esc_html_e('Reading the table', 'compareoxygen'); ?></p>
                <h2 class="section-title"><?php esc_html_e('What the comparison does and does not say', 'compareoxygen'); ?></h2>
                <p class="section-intro"><?php esc_html_e('The table is a research aid. It is deliberately not a scorecard.', 'compareoxygen'); ?></p>
            </div>
            <div class="feature-grid">
                <article class="feature-cell">
                    <span class="feature-cell__icon"><?php echo compareoxygen_icon('layers'); ?></span>
                    <h3><?php esc_html_e('Grouped by decision', 'compareoxygen'); ?></h3>
                    <p><?php esc_html_e('Rows follow the questions buyers actually ask: how heavy is it, how long does the battery last, how loud is it, and what does the warranty cover?', 'compareoxygen'); ?></p>
                </article>
                <article class="feature-cell">
                    <span class="feature-cell__icon"><?php echo compareoxygen_icon('spark'); ?></span>
                    <h3><?php esc_html_e('Differences highlighted', 'compareoxygen'); ?></h3>
                    <p><?php esc_html_e('Shading marks rows where the published values differ. “Differences only” hides matching rows, so you can scan the trade-offs quickly.', 'compareoxygen'); ?></p>
                </article>
                <article class="feature-cell">
                    <span class="feature-cell__icon"><?php echo compareoxygen_icon('alert'); ?></span>
                    <h3><?php esc_html_e('Gaps left visible', 'compareoxygen'); ?></h3>
                    <p><?php esc_html_e('A blank cell is not a zero. It means the manufacturer does not publish the figure or our editor has not verified it. Ask the supplier if the gap matters to you.', 'compareoxygen'); ?></p>
                </article>
            </div>
        </div>
    </section>

    <?php if ($comparisons) : ?>
        <section class="section">
            <div class="container">
                <div class="section-head">
                    <p class="eyebrow"><?php esc_html_e('Editor’s shortlist', 'compareoxygen'); ?></p>
                    <h2 class="section-title"><?php esc_html_e('Comparisons we have already published', 'compareoxygen'); ?></h2>
                    <p class="section-intro"><?php esc_html_e('Approved comparisons pair two reviewed models and add editorial context. The comparison tool also gives every pair a clean, shareable URL.', 'compareoxygen'); ?></p>
                </div>
                <div class="comparison-list">
                    <?php foreach ($comparisons as $comparison) : ?>
                        <?php get_template_part('template-parts/comparison', 'card', ['comparison_id' => $comparison->ID]); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="section section--white">
        <div class="container container--reading">
            <div class="section-head">
                <p class="eyebrow"><?php esc_html_e('Comparison questions', 'compareoxygen'); ?></p>
                <h2 class="section-title"><?php esc_html_e('Before you rely on a table', 'compareoxygen'); ?></h2>
            </div>
            <?php compareoxygen_faq($faqs); ?>
            <?php compareoxygen_faq_schema($faqs); ?>
            <p class="mt-3"><a class="link-arrow" href="<?php echo esc_url(compareoxygen_page_url('how-we-rate')); ?>"><?php esc_html_e('Read the full methodology', 'compareoxygen'); ?><?php echo compareoxygen_icon('arrow-right'); ?></a></p>
        </div>
    </section>

    <div class="container">
        <?php
        compareoxygen_cta_band(
            __('Need the broader picture first?', 'compareoxygen'),
            __('The Best Of guide explains how our editors assign awards, and the buying guides cover the questions worth asking before a shortlist exists.', 'compareoxygen'),
            __('Read the Best Of guide', 'compareoxygen'),
            compareoxygen_page_url('best', '/best/portable-oxygen-concentrators/'),
            __('Browse buying guides', 'compareoxygen'),
            compareoxygen_page_url('guides')
        );
        ?>
    </div>
</main>
<?php
get_footer();
