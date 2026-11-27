<?php
/**
 * Full specification table with per-fact evidence.
 *
 * @package compareoxygen
 */

$model_id = absint($args['model_id'] ?? get_the_ID());
if (!$model_id || get_post_type($model_id) !== 'poc_model') { return; }

$verified = coc_verified_count($model_id);
$total = count(COC_Core::SPECS);
$grouped = [];
foreach (COC_Core::SPECS as $key => $meta) {
    $grouped[$meta['group']][$key] = $meta;
}
?>
<div class="spec-block">
    <div class="spec-block__head">
        <p class="mb-0"><strong><?php printf(esc_html__('%1$d of %2$d fields verified', 'compareoxygen'), (int) $verified, (int) $total); ?></strong> — <?php esc_html_e('unverified fields are shown as “Not confirmed”, never estimated.', 'compareoxygen'); ?></p>
        <a class="link-arrow" href="<?php echo esc_url(add_query_arg(['first' => $model_id], coc_compare_tool_url())); ?>"><?php esc_html_e('Compare side by side', 'compareoxygen'); ?><?php echo compareoxygen_icon('arrow-right'); ?></a>
    </div>

    <div class="table-scroll">
        <table class="spec-table">
            <caption><?php printf(esc_html__('Published specifications for %s, with the manufacturer source and verification date for each confirmed fact.', 'compareoxygen'), esc_html(get_the_title($model_id))); ?></caption>
            <thead>
                <tr>
                    <th scope="col"><?php esc_html_e('Specification', 'compareoxygen'); ?></th>
                    <th scope="col"><?php esc_html_e('Published value', 'compareoxygen'); ?></th>
                    <th scope="col"><?php esc_html_e('Evidence', 'compareoxygen'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grouped as $group_key => $fields) : ?>
                    <tr class="spec-group">
                        <th colspan="3" scope="colgroup"><?php echo esc_html(COC_Core::GROUPS[$group_key] ?? $group_key); ?></th>
                    </tr>
                    <?php foreach ($fields as $key => $meta) : ?>
                        <?php $spec = coc_spec($model_id, $key); ?>
                        <tr>
                            <th scope="row"><?php echo esc_html($meta['label']); ?></th>
                            <td>
                                <?php if ($spec['complete']) : ?>
                                    <span class="num"><?php echo esc_html($spec['display']); ?></span>
                                <?php else : ?>
                                    <span class="unconfirmed"><?php esc_html_e('Not confirmed', 'compareoxygen'); ?></span>
                                    <span class="spec-evidence"><?php esc_html_e('The manufacturer does not publish this figure, or our team has not verified it against an official source.', 'compareoxygen'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($spec['complete']) : ?>
                                    <a class="compare-source" href="<?php echo esc_url($spec['source']); ?>" target="_blank" rel="nofollow noopener"><?php echo compareoxygen_icon('arrow-up-right'); ?><?php echo esc_html((string) wp_parse_url($spec['source'], PHP_URL_HOST)); ?></a>
                                    <span class="compare-date"><?php printf(esc_html__('Verified %s', 'compareoxygen'), esc_html(compareoxygen_format_date($spec['verified']))); ?></span>
                                <?php else : ?>
                                    <span class="muted small">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <p class="compare-note">
        <?php echo compareoxygen_icon('info'); ?>
        <span><?php esc_html_e('Manufacturer specifications can change without notice. Confirm any figure that affects your prescription, travel plans, or purchase directly with the manufacturer or your oxygen supplier.', 'compareoxygen'); ?></span>
    </p>
</div>
