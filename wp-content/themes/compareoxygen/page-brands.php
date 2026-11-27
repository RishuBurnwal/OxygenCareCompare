<?php
/**
 * Template Name: Brands hub
 * Description: Manufacturer index with documented models.
 *
 * @package compareoxygen
 */

get_header();

$brands = get_terms(['taxonomy' => 'poc_brand', 'hide_empty' => true]);
$total_models = count(coc_models());
?>
<main id="main-content">
    <?php compareoxygen_breadcrumbs(); ?>

    <section class="page-hero page-hero--mint">
        <div class="container page-hero__inner">
            <p class="eyebrow"><?php esc_html_e('Brands', 'compareoxygen'); ?></p>
            <h1><?php esc_html_e('Manufacturers we have documented', 'compareoxygen'); ?></h1>
            <p class="page-hero__lede"><?php esc_html_e('Each brand page collects the models we have reviewed and the facts we could verify from that manufacturer’s own documentation. A brand’s absence here means we have not published a record yet, not that it lacks products.', 'compareoxygen'); ?></p>
            <div class="page-hero__meta">
                <span><?php echo compareoxygen_icon('layers'); ?><?php echo esc_html(sprintf(_n('%s brand documented', '%s brands documented', is_wp_error($brands) ? 0 : count($brands), 'compareoxygen'), number_format_i18n(is_wp_error($brands) ? 0 : count($brands)))); ?></span>
                <span><?php echo compareoxygen_icon('check'); ?><?php echo esc_html(sprintf(_n('%s model record', '%s model records', $total_models, 'compareoxygen'), number_format_i18n($total_models))); ?></span>
            </div>
        </div>
    </section>

    <section class="section section--tight">
        <div class="container">
            <?php if (!is_wp_error($brands) && $brands) : ?>
                <h2 class="screen-reader-text"><?php esc_html_e('Documented manufacturers', 'compareoxygen'); ?></h2>
                <div class="brand-grid">
                    <?php foreach ($brands as $brand) : ?>
                        <a class="brand-card" href="<?php echo esc_url(get_term_link($brand)); ?>">
                            <?php compareoxygen_brand_mark($brand->name); ?>
                            <div>
                                <h3><?php echo esc_html($brand->name); ?></h3>
                                <p><?php echo esc_html($brand->description ?: __('Models documented from manufacturer sources.', 'compareoxygen')); ?></p>
                            </div>
                            <span class="brand-card__count"><?php echo esc_html(sprintf(_n('%s reviewed model', '%s reviewed models', $brand->count, 'compareoxygen'), number_format_i18n($brand->count))); ?><?php echo compareoxygen_icon('arrow-right'); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="empty-state">
                    <h2><?php esc_html_e('Brand pages are being prepared', 'compareoxygen'); ?></h2>
                    <p><?php esc_html_e('Brand pages appear automatically once a reviewed model is assigned to a manufacturer.', 'compareoxygen'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="section section--white">
        <div class="container container--reading">
            <div class="section-head">
                <p class="eyebrow"><?php esc_html_e('How brand pages work', 'compareoxygen'); ?></p>
                <h2 class="section-title"><?php esc_html_e('No brand profiles, no marketing copy', 'compareoxygen'); ?></h2>
            </div>
            <div class="prose">
                <p><?php esc_html_e('A brand page is an index, not a profile. We do not publish “about the brand” narratives assembled from marketing material, and we do not rank manufacturers against one another. The page exists to group models that share a manufacturer so that readers can compare within a family or across families.', 'compareoxygen'); ?></p>
                <h3><?php esc_html_e('What appears on a brand page', 'compareoxygen'); ?></h3>
                <ul>
                    <li><?php esc_html_e('Every reviewed model from that manufacturer, with only verified specifications on the cards.', 'compareoxygen'); ?></li>
                    <li><?php esc_html_e('A link to each model’s full evidence record, including sources and verification dates.', 'compareoxygen'); ?></li>
                    <li><?php esc_html_e('Direct links into the comparison tool, pre-filled with that model.', 'compareoxygen'); ?></li>
                </ul>
                <h3><?php esc_html_e('What does not appear', 'compareoxygen'); ?></h3>
                <ul>
                    <li><?php esc_html_e('Star ratings or “brand scores” invented for the site.', 'compareoxygen'); ?></li>
                    <li><?php esc_html_e('Claims about a manufacturer’s service network unless the manufacturer publishes them and we cite the source.', 'compareoxygen'); ?></li>
                    <li><?php esc_html_e('Paid brand placement. Commercial relationships are disclosed site-wide and never determine inclusion.', 'compareoxygen'); ?></li>
                </ul>
            </div>
        </div>
    </section>

    <div class="container">
        <?php
        compareoxygen_cta_band(
            __('Compare across manufacturers', 'compareoxygen'),
            __('The comparison tool treats every brand the same way: manufacturer-stated values, a source link, and a visible gap where the record is incomplete.', 'compareoxygen'),
            __('Open the comparison tool', 'compareoxygen'),
            coc_compare_tool_url()
        );
        ?>
    </div>
</main>
<?php
get_footer();
