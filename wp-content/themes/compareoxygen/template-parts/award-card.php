<?php
/**
 * Award card (shortcode and editorial use).
 *
 * @package compareoxygen
 */

$model = $args['model'] ?? null;
if (!$model instanceof WP_Post || $model->post_type !== 'poc_model') { return; }
$label = (string) ($args['label'] ?? '');
if ($label === '') { $label = coc_award_label($model->ID) ?: __('Editorial selection', 'compareoxygen'); }
$weight = coc_spec($model->ID, 'weight')['display'];
$battery = coc_spec($model->ID, 'battery_extended')['display'] ?: coc_spec($model->ID, 'battery_standard')['display'];
?>
<article class="award-card">
    <span class="award-card__label"><?php echo compareoxygen_icon('spark'); ?><?php echo esc_html($label); ?></span>
    <h3><a href="<?php echo esc_url(get_permalink($model)); ?>"><?php echo esc_html(get_the_title($model)); ?></a></h3>
    <p class="award-card__brand"><?php echo esc_html(coc_brand_of($model->ID)); ?></p>
    <p><?php echo esc_html(wp_trim_words(get_the_excerpt($model->ID), 24)); ?></p>
    <div class="award-card__specs">
        <div><?php esc_html_e('Weight', 'compareoxygen'); ?><strong><?php echo $weight ? esc_html($weight) : esc_html__('Not confirmed', 'compareoxygen'); ?></strong></div>
        <div><?php esc_html_e('Battery', 'compareoxygen'); ?><strong><?php echo $battery ? esc_html($battery) : esc_html__('Not confirmed', 'compareoxygen'); ?></strong></div>
    </div>
    <div class="button-row mt-3">
        <a class="btn btn--primary btn--sm" href="<?php echo esc_url(get_permalink($model)); ?>"><?php esc_html_e('Read the review', 'compareoxygen'); ?></a>
        <a class="btn btn--ghost btn--sm" href="<?php echo esc_url(add_query_arg(['first' => $model->ID], coc_compare_tool_url())); ?>"><?php esc_html_e('Compare', 'compareoxygen'); ?><?php echo compareoxygen_icon('arrow-right'); ?></a>
    </div>
</article>
