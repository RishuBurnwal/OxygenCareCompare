<?php
/**
 * Comparison results: full product summary, sticky mini cards, section nav,
 * and the evidence table. Renders two to four models.
 *
 * @package compareoxygen
 */

$models = isset($args['models']) ? (array) $args['models'] : [absint($args['first'] ?? 0), absint($args['second'] ?? 0)];
$models = array_values(array_filter(array_map('absint', $models)));
$models = class_exists('COC_Core') ? COC_Core::sanitize_model_ids($models) : array_slice($models, 0, 4);
$standalone = !empty($args['standalone']);
if (count($models) < 2) { return; }

$model_posts = [];
foreach ($models as $model_id) {
    $model_post = get_post($model_id);
    if (!$model_post instanceof WP_Post || $model_post->post_type !== 'poc_model') { return; }
    $model_posts[] = $model_post;
}
$count = count($model_posts);
$first_post = $model_posts[0];
$second_post = $model_posts[1];

$groups = COC_Core::comparison_groups_for($models);
$differences = COC_Core::difference_count_for($models);
$verified = COC_Core::min_verified_for($models);
$approved = $count === 2 ? coc_comparison_for_pair($models[0], $models[1]) : null;
$approved_url = $approved ? get_permalink($approved) : '';
$pair_url = coc_compare_url($models);
$tool_url = coc_compare_tool_url();

$card_facts = static function (int $model_id): array {
    $facts = [];
    foreach ([
        'weight' => __('Weight', 'compareoxygen'),
        'battery_standard' => __('Battery', 'compareoxygen'),
    ] as $key => $label) {
        $spec = coc_spec($model_id, $key);
        if ($spec['complete']) {
            $facts[] = $label . ': ' . $spec['display'];
        }
    }
    return $facts;
};

$add_models = [];
if ($count < 4) {
    foreach (coc_models() as $candidate) {
        if (!in_array((int) $candidate->ID, $models, true)) { $add_models[] = $candidate; }
    }
}

$model_col = 75 / $count;
$model_col_css = rtrim(rtrim(number_format($model_col, 4, '.', ''), '0'), '.') . '%';
if ($add_models) {
    // Reserve a narrow trailing track for the sticky "+" so the cards stay
    // close to their table columns.
    $sticky_cols = '25%' . str_repeat(' calc((75% - 60px) / ' . $count . ')', $count) . ' 60px';
} else {
    $sticky_cols = '25%' . str_repeat(' ' . $model_col_css, $count);
}

$render_add_list = static function () use ($add_models, $models): void { ?>
    <ul class="compare-add__list">
        <?php foreach ($add_models as $candidate) : ?>
            <?php
            $candidate_brand = coc_brand_of($candidate->ID);
            $candidate_name = $candidate->post_title;
            // The row already labels the brand, so avoid "Inogen Inogen Rove 6".
            if ($candidate_brand !== '' && stripos($candidate_name, $candidate_brand) === 0) {
                $trimmed_name = trim(substr($candidate_name, strlen($candidate_brand)));
                if ($trimmed_name !== '') { $candidate_name = $trimmed_name; }
            }
            ?>
            <li>
                <a href="<?php echo esc_url(coc_compare_url(array_merge($models, [(int) $candidate->ID]))); ?>">
                    <span class="compare-add__brand"><?php echo esc_html($candidate_brand); ?></span>
                    <span class="compare-add__name"><?php echo esc_html($candidate_name); ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php };
?>
<section class="compare-results" id="comparison-results" aria-labelledby="comparison-heading">
    <div class="compare-sticky<?php echo $count > 2 ? ' compare-sticky--multi' : ''; ?><?php echo $count > 3 ? ' compare-sticky--dense' : ''; ?><?php echo $add_models ? ' compare-sticky--has-add' : ''; ?>" data-compare-sticky aria-hidden="true" inert>
        <div class="compare-sticky__inner" style="grid-template-columns: <?php echo esc_attr($sticky_cols); ?>">
            <div class="compare-sticky__pair">
                <div class="compare-sticky__cards">
                    <?php foreach ($model_posts as $index => $model_post) : ?>
                        <?php $facts = $card_facts($model_post->ID); $price = coc_spec($model_post->ID, 'price'); ?>
                        <a class="compare-mini" href="<?php echo esc_url(get_permalink($model_post)); ?>">
                            <span class="compare-mini__art" aria-hidden="true"><?php compareoxygen_device_illustration('compact', 'teal'); ?></span>
                            <span class="compare-mini__text">
                                <span class="compare-mini__brand"><?php echo esc_html(coc_brand_of($model_post->ID)); ?></span>
                                <span class="compare-mini__name"><?php echo esc_html($model_post->post_title); ?></span>
                                <?php if ($facts) : ?><span class="compare-mini__facts"><?php echo esc_html(implode(' · ', $facts)); ?></span><?php endif; ?>
                                <?php if ($price['complete']) : ?><span class="compare-mini__price"><?php echo esc_html($price['display']); ?></span><?php endif; ?>
                            </span>
                            <span class="compare-mini__go" aria-hidden="true"><?php echo compareoxygen_icon('arrow-right'); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php if ($add_models) : ?>
                    <details class="compare-sticky__add" data-compare-add style="--compare-add-col: <?php echo esc_attr($count + 2); ?>;">
                        <summary class="compare-sticky__plus">
                            <span class="compare-sticky__plus-icon" aria-hidden="true"><?php echo compareoxygen_icon('plus'); ?></span>
                            <span class="compare-sticky__plus-label"><?php esc_html_e('Add', 'compareoxygen'); ?></span>
                            <span class="screen-reader-text"><?php esc_html_e('Add another product to this comparison', 'compareoxygen'); ?></span>
                        </summary>
                        <div class="compare-add__panel compare-add__panel--sticky">
                            <p class="compare-add__panel-title"><?php esc_html_e('Compare another concentrator', 'compareoxygen'); ?></p>
                            <?php $render_add_list(); ?>
                        </div>
                    </details>
                <?php endif; ?>
            </div>
            <div class="compare-sticky__actions">
                <button class="ghost-btn ghost-btn--compact" type="button" data-toggle-highlight aria-pressed="true"><?php echo compareoxygen_icon('spark'); ?><?php esc_html_e('Highlight', 'compareoxygen'); ?></button>
                <button class="ghost-btn ghost-btn--compact" type="button" data-toggle-diff aria-pressed="false"><?php echo compareoxygen_icon('list'); ?><?php esc_html_e('Differences', 'compareoxygen'); ?></button>
                <a class="ghost-btn ghost-btn--compact" href="<?php echo esc_url($tool_url); ?>"><?php esc_html_e('Change', 'compareoxygen'); ?></a>
            </div>
        </div>
    </div>

    <div class="compare-products<?php echo $count > 2 ? ' compare-products--multi' : ''; ?>" data-compare-products>
        <?php foreach ($model_posts as $index => $model_post) : ?>
            <?php $facts = $card_facts($model_post->ID); $price = coc_spec($model_post->ID, 'price'); ?>
            <article class="compare-hero-card">
                <a class="compare-hero-card__link" href="<?php echo esc_url(get_permalink($model_post)); ?>">
                    <span class="compare-hero-card__art" aria-hidden="true"><?php compareoxygen_device_illustration('compact', 'teal'); ?></span>
                    <span class="compare-hero-card__body">
                        <span class="compare-hero-card__brand"><?php echo esc_html(coc_brand_of($model_post->ID)); ?></span>
                        <span class="compare-hero-card__name"><?php echo esc_html($model_post->post_title); ?></span>
                        <?php if ($facts) : ?><span class="compare-hero-card__facts"><?php echo esc_html(implode(' · ', $facts)); ?></span><?php endif; ?>
                        <?php if ($price['complete']) : ?><span class="compare-hero-card__price"><?php echo esc_html($price['display']); ?></span><?php endif; ?>
                        <span class="compare-hero-card__cta"><?php esc_html_e('Review and sources', 'compareoxygen'); ?><?php echo compareoxygen_icon('arrow-right'); ?></span>
                    </span>
                </a>
            </article>
            <?php if ($index < $count - 1) : ?>
                <span class="compare-vs" aria-hidden="true"><?php esc_html_e('VS', 'compareoxygen'); ?></span>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($add_models) : ?>
            <details class="compare-add" data-compare-add>
                <summary class="compare-add__tile">
                    <span class="compare-add__plus" aria-hidden="true"><?php echo compareoxygen_icon('plus'); ?></span>
                    <span class="compare-add__text">
                        <span class="compare-add__label"><?php esc_html_e('Add a product', 'compareoxygen'); ?></span>
                        <span class="compare-add__hint"><?php printf(esc_html(_n('%s spot left', '%s spots left', 4 - $count, 'compareoxygen')), esc_html(number_format_i18n(4 - $count))); ?></span>
                    </span>
                </summary>
                <div class="compare-add__panel">
                    <p class="compare-add__panel-title"><?php esc_html_e('Compare another concentrator', 'compareoxygen'); ?></p>
                    <?php $render_add_list(); ?>
                </div>
            </details>
        <?php endif; ?>
    </div>

    <div class="compare-toolbar">
        <div class="compare-toolbar__title">
            <?php if ($standalone) : ?>
                <h2 id="comparison-heading" class="compare-table-title"><?php esc_html_e('Specification table', 'compareoxygen'); ?></h2>
                <p class="muted small mb-0"><?php printf(esc_html(_n('%s published difference', '%s published differences', $differences, 'compareoxygen')), esc_html(number_format_i18n($differences))); ?><?php if ($verified) : ?> · <?php printf(esc_html__('checked %s', 'compareoxygen'), esc_html(compareoxygen_format_date($verified))); ?><?php endif; ?></p>
            <?php else : ?>
                <h2 id="comparison-heading" class="clamp-mobile" style="margin:0 0 .2rem;font-size:1.45rem;"><?php echo esc_html(sprintf(__('%1$s vs %2$s', 'compareoxygen'), $first_post->post_title, $second_post->post_title)); ?></h2>
                <p class="muted small mb-0"><?php printf(esc_html(_n('%s difference in published specifications', '%s differences in published specifications', $differences, 'compareoxygen')), esc_html(number_format_i18n($differences))); ?></p>
            <?php endif; ?>
        </div>
        <div class="compare-actions">
            <button class="ghost-btn" type="button" data-toggle-highlight aria-pressed="true"><?php echo compareoxygen_icon('spark'); ?><?php esc_html_e('Highlight differences', 'compareoxygen'); ?></button>
            <button class="ghost-btn" type="button" data-toggle-diff aria-pressed="false"><?php echo compareoxygen_icon('list'); ?><?php esc_html_e('Differences only', 'compareoxygen'); ?></button>
            <button class="ghost-btn" type="button" data-toggle-sources aria-pressed="true"><?php echo compareoxygen_icon('file'); ?><?php esc_html_e('Sources', 'compareoxygen'); ?></button>
            <button class="ghost-btn" type="button" data-share="<?php echo esc_url($pair_url); ?>"><?php echo compareoxygen_icon('arrow-up-right'); ?><span data-share-label><?php esc_html_e('Share', 'compareoxygen'); ?></span></button>
        </div>
    </div>

    <nav class="compare-section-nav" data-compare-nav aria-label="<?php esc_attr_e('Specification sections', 'compareoxygen'); ?>">
        <div class="compare-section-nav__links">
            <?php foreach ($groups as $group_key => $rows) : ?>
                <a href="#spec-<?php echo esc_attr(sanitize_html_class($group_key)); ?>" data-compare-nav-link><?php echo esc_html(COC_Core::GROUPS[$group_key] ?? $group_key); ?></a>
            <?php endforeach; ?>
        </div>
        <label class="compare-search">
            <span class="screen-reader-text"><?php esc_html_e('Search specifications', 'compareoxygen'); ?></span>
            <?php echo compareoxygen_icon('search'); ?>
            <input type="search" placeholder="<?php esc_attr_e('Search a feature or spec', 'compareoxygen'); ?>" data-compare-search>
        </label>
    </nav>

    <div class="compare-shell">
        <div class="compare-scroll">
            <table class="compare-table" data-compare-table data-model-count="<?php echo esc_attr($count); ?>">
                <caption>
                    <?php esc_html_e('Specifications published by each manufacturer and verified on the date shown. Blank cells mean the manufacturer does not publish the figure or our team has not yet verified it — we do not estimate missing values.', 'compareoxygen'); ?>
                </caption>
                <colgroup>
                    <col class="compare-col-spec">
                    <?php for ($index = 0; $index < $count; $index++) : ?>
                        <col class="compare-col-model" style="width: <?php echo esc_attr($model_col_css); ?>">
                    <?php endfor; ?>
                </colgroup>
                <thead>
                    <tr>
                        <th scope="col"><?php esc_html_e('Specification', 'compareoxygen'); ?></th>
                        <?php foreach ($model_posts as $model_post) : ?>
                            <th scope="col">
                                <span class="compare-table__brand"><?php echo esc_html(coc_brand_of($model_post->ID)); ?></span>
                                <span class="compare-table__model"><?php echo esc_html($model_post->post_title); ?></span>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($groups as $group_key => $rows) : ?>
                        <tr class="compare-group" id="spec-<?php echo esc_attr(sanitize_html_class($group_key)); ?>" data-spec-group="<?php echo esc_attr($group_key); ?>">
                            <th colspan="<?php echo esc_attr($count + 1); ?>" scope="colgroup"><?php echo esc_html(COC_Core::GROUPS[$group_key] ?? $group_key); ?></th>
                        </tr>
                        <?php foreach ($rows as $row) : ?>
                            <tr class="<?php echo $row['same'] ? 'is-same' : 'is-different'; ?>" data-diff="<?php echo $row['same'] ? 'false' : 'true'; ?>" data-spec-row data-spec-label="<?php echo esc_attr($row['label']); ?>" data-spec-group="<?php echo esc_attr($group_key); ?>">
                                <th scope="row"><?php echo esc_html($row['label']); ?></th>
                                <?php foreach ($row['specs'] as $index => $spec) : ?>
                                    <?php $side_title = $model_posts[$index]->post_title; ?>
                                    <td data-model="<?php echo esc_attr($side_title); ?>">
                                        <?php if ($spec['complete']) : ?>
                                            <span class="compare-value"><?php echo esc_html($spec['display']); ?></span>
                                            <span class="compare-source-wrap">
                                                <a class="compare-source" href="<?php echo esc_url($spec['source']); ?>" target="_blank" rel="nofollow noopener"><?php echo compareoxygen_icon('arrow-up-right'); ?><?php esc_html_e('Manufacturer source', 'compareoxygen'); ?></a>
                                                <span class="compare-date"><?php printf(esc_html__('Verified %s', 'compareoxygen'), esc_html(compareoxygen_format_date($spec['verified']))); ?></span>
                                            </span>
                                        <?php else : ?>
                                            <span class="compare-missing"><?php echo compareoxygen_icon('minus'); ?><?php esc_html_e('Not confirmed', 'compareoxygen'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <p class="compare-empty" data-compare-empty hidden><?php esc_html_e('No specification matches that search. Try a shorter term, or clear the search to see every row.', 'compareoxygen'); ?></p>

    <div class="compare-note">
        <?php echo compareoxygen_icon('info'); ?>
        <p><?php esc_html_e('Differences are highlighted as a reading aid, not as a recommendation. A larger number is not automatically better: battery life, weight, and oxygen delivery interact with each person’s prescription and routine. Confirm clinical suitability with your clinician.', 'compareoxygen'); ?><?php if ($verified) { echo ' ' . esc_html(sprintf(__('The oldest verified figure on this page was checked on %s.', 'compareoxygen'), compareoxygen_format_date($verified))); } ?></p>
    </div>

    <div class="button-row mt-3">
        <?php foreach ($model_posts as $model_post) : ?>
            <a class="btn btn--primary btn--sm" href="<?php echo esc_url(get_permalink($model_post)); ?>"><?php printf(esc_html__('Read the %s review', 'compareoxygen'), esc_html($model_post->post_title)); ?></a>
        <?php endforeach; ?>
        <a class="btn btn--ghost btn--sm" href="<?php echo esc_url($tool_url); ?>"><?php esc_html_e('Change models', 'compareoxygen'); ?></a>
        <?php if ($approved) : ?>
            <a class="btn btn--ghost btn--sm" href="<?php echo esc_url(get_permalink($approved)); ?>"><?php esc_html_e('Read the editorial comparison', 'compareoxygen'); ?></a>
        <?php endif; ?>
    </div>
</section>
