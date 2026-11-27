<?php
/**
 * Approved comparison card.
 *
 * @package compareoxygen
 */

$comparison_id = absint($args['comparison_id'] ?? 0);
if (!$comparison_id || get_post_type($comparison_id) !== 'poc_comparison') { return; }
[$first_id, $second_id] = COC_Core::comparison_ids($comparison_id);
$first = get_post($first_id);
$second = get_post($second_id);
if (!$first instanceof WP_Post || !$second instanceof WP_Post) { return; }
if ($first->post_type !== 'poc_model' || $second->post_type !== 'poc_model') { return; }
$differences = COC_Core::difference_count($first_id, $second_id);
$verified = COC_Core::min_verified_between($first_id, $second_id);
?>
<a class="comparison-card" href="<?php echo esc_url(get_permalink($comparison_id)); ?>">
    <span class="comparison-card__label"><?php echo compareoxygen_icon('scale'); ?><?php esc_html_e('Approved comparison', 'compareoxygen'); ?></span>
    <span class="comparison-card__pair">
        <span class="comparison-card__name"><?php echo esc_html($first->post_title); ?></span>
        <span class="comparison-card__vs"><?php esc_html_e('vs', 'compareoxygen'); ?></span>
        <span class="comparison-card__name"><?php echo esc_html($second->post_title); ?></span>
    </span>
    <span class="comparison-card__meta">
        <?php if ($differences) : ?><?php printf(esc_html(_n('%s published difference', '%s published differences', $differences, 'compareoxygen')), esc_html(number_format_i18n($differences))); ?><?php endif; ?>
        <?php if ($verified) : ?><span><?php printf(esc_html__('Checked %s', 'compareoxygen'), esc_html(compareoxygen_format_date($verified))); ?></span><?php endif; ?>
    </span>
    <span class="comparison-card__cta"><?php esc_html_e('Read the comparison', 'compareoxygen'); ?><?php echo compareoxygen_icon('arrow-right'); ?></span>
</a>
