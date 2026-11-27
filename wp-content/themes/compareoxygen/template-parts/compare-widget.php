<?php
/**
 * Compare selector widget.
 *
 * @package compareoxygen
 */

$models = $args['models'] ?? coc_models();
$first = absint($args['first'] ?? 0);
$second = absint($args['second'] ?? 0);
$compact = !empty($args['compact']);
$action = coc_compare_tool_url();

$model_data = [];
foreach ($models as $model) {
    $weight = coc_spec($model->ID, 'weight')['display'];
    $model_data[] = [
        'id' => (int) $model->ID,
        'name' => get_the_title($model),
        'brand' => coc_brand_of($model->ID),
        'slug' => $model->post_name,
        'weight' => $weight,
    ];
}
?>
<div class="compare-widget" data-compare-widget>
    <?php if (!$compact) : ?>
        <div class="compare-widget__head">
            <div>
                <h2><?php esc_html_e('Compare two concentrators', 'compareoxygen'); ?></h2>
                <p><?php esc_html_e('Pick any two published models. Missing facts stay visible as “Not confirmed”.', 'compareoxygen'); ?></p>
            </div>
            <a class="link-arrow" href="<?php echo esc_url(get_post_type_archive_link('poc_comparison') ?: home_url('/comparisons/')); ?>"><?php esc_html_e('Browse editorial comparisons', 'compareoxygen'); ?><?php echo compareoxygen_icon('arrow-right'); ?></a>
        </div>
    <?php endif; ?>

    <form class="compare-widget__form<?php echo $compact ? ' is-compact' : ''; ?>" method="get" action="<?php echo esc_url($action); ?>" data-compare-form>
        <?php foreach ([['first', __('First concentrator', 'compareoxygen'), $first], ['second', __('Second concentrator', 'compareoxygen'), $second]] as $index => $field) : ?>
            <?php
            $key = $field[0];
            $selected = $field[2];
            $selected_model = $selected ? get_post($selected) : null;
            $selected_name = $selected_model instanceof WP_Post && $selected_model->post_type === 'poc_model' ? $selected_model->post_title : '';
            ?>
            <div class="combo" data-combo="<?php echo esc_attr($key); ?>">
                <label class="combo__label" for="combo-<?php echo esc_attr($key); ?>"><?php echo esc_html($field[1]); ?></label>
                <div class="combo__control">
                    <input
                        class="combo__input"
                        id="combo-<?php echo esc_attr($key); ?>"
                        type="text"
                        role="combobox"
                        aria-expanded="false"
                        aria-autocomplete="list"
                        aria-controls="combo-<?php echo esc_attr($key); ?>-list"
                        autocomplete="off"
                        placeholder="<?php esc_attr_e('Search brand or model', 'compareoxygen'); ?>"
                        value="<?php echo esc_attr($selected_name); ?>">
                    <span class="combo__chevron"><?php echo compareoxygen_icon('chevron-down'); ?></span>
                    <input type="hidden" name="<?php echo esc_attr($key); ?>" value="<?php echo $selected ? absint($selected) : ''; ?>">
                    <ul class="combo__list" id="combo-<?php echo esc_attr($key); ?>-list" role="listbox" aria-label="<?php echo esc_attr($field[1]); ?>" hidden></ul>
                </div>
            </div>
            <?php if ($index === 0) : ?>
                <button class="swap-btn" type="button" data-swap-models aria-label="<?php esc_attr_e('Swap the two selected concentrators', 'compareoxygen'); ?>">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M7 4 4 7l3 3M4 7h11a4 4 0 0 1 4 4M17 20l3-3-3-3m3 3H9a4 4 0 0 1-4-4"/></svg>
                </button>
            <?php endif; ?>
        <?php endforeach; ?>

        <button class="btn btn--primary compare-widget__submit" type="submit"><?php echo compareoxygen_icon('scale'); ?><?php esc_html_e('Compare now', 'compareoxygen'); ?></button>

        <noscript>
            <p class="muted small"><?php esc_html_e('JavaScript is off, so use these lists instead.', 'compareoxygen'); ?></p>
            <label class="combo__label" for="fallback-first"><?php esc_html_e('First', 'compareoxygen'); ?></label>
            <select id="fallback-first" name="first"><?php echo COC_Core::model_options($models, $first); ?></select>
            <label class="combo__label" for="fallback-second"><?php esc_html_e('Second', 'compareoxygen'); ?></label>
            <select id="fallback-second" name="second"><?php echo COC_Core::model_options($models, $second); ?></select>
        </noscript>
    </form>

    <p class="compare-error" data-compare-error></p>

    <script type="application/json" id="coc-model-data"><?php echo wp_json_encode($model_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
</div>
