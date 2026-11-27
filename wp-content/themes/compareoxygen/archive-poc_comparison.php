<?php
/**
 * Approved comparison archive.
 *
 * @package compareoxygen
 */

get_header();

$total = (int) $GLOBALS['wp_query']->found_posts;
?>
<main id="main-content">
    <?php compareoxygen_breadcrumbs(); ?>

    <section class="page-hero page-hero--mint">
        <div class="container page-hero__inner">
            <p class="eyebrow"><?php esc_html_e('Comparison library', 'compareoxygen'); ?></p>
            <h1><?php esc_html_e('Portable oxygen concentrator comparisons', 'compareoxygen'); ?></h1>
            <p class="page-hero__lede"><?php esc_html_e('Editor-approved pairings with the full evidence table. Each comparison has its own URL and a written rationale; generated pair permutations are kept out of search.', 'compareoxygen'); ?></p>
            <div class="page-hero__meta">
                <span><?php echo compareoxygen_icon('scale'); ?><?php echo esc_html(sprintf(_n('%s approved comparison', '%s approved comparisons', $total, 'compareoxygen'), number_format_i18n($total))); ?></span>
                <span><?php echo compareoxygen_icon('shield'); ?><?php esc_html_e('Order implies no ranking', 'compareoxygen'); ?></span>
            </div>
            <div class="button-row mt-3">
                <a class="btn btn--primary" href="<?php echo esc_url(coc_compare_tool_url()); ?>"><?php echo compareoxygen_icon('scale'); ?><?php esc_html_e('Build your own comparison', 'compareoxygen'); ?></a>
            </div>
        </div>
    </section>

    <div class="container">
        <?php compareoxygen_disclosure(); ?>
    </div>

    <section class="section section--tight">
        <div class="container">
            <?php if (have_posts()) : ?>
                <div class="comparison-list">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php get_template_part('template-parts/comparison', 'card', ['comparison_id' => get_the_ID()]); ?>
                    <?php endwhile; ?>
                </div>
                <div class="pagination">
                    <?php
                    the_posts_pagination([
                        'mid_size' => 1,
                        'prev_text' => __('Previous', 'compareoxygen'),
                        'next_text' => __('Next', 'compareoxygen'),
                        'screen_reader_text' => __('Comparisons pagination', 'compareoxygen'),
                    ]);
                    ?>
                </div>
            <?php else : ?>
                <div class="empty-state">
                    <h2><?php esc_html_e('No approved comparisons yet', 'compareoxygen'); ?></h2>
                    <p><?php esc_html_e('Our editors publish a standalone comparison when a pairing deserves analysis beyond the raw table. In the meantime, the comparison tool works with any two published models.', 'compareoxygen'); ?></p>
                    <p><a class="btn btn--primary" href="<?php echo esc_url(coc_compare_tool_url()); ?>"><?php esc_html_e('Open the comparison tool', 'compareoxygen'); ?></a></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="section section--white">
        <div class="container container--reading">
            <div class="section-head">
                <p class="eyebrow"><?php esc_html_e('Editorial versus generated', 'compareoxygen'); ?></p>
                <h2 class="section-title"><?php esc_html_e('Why most pairs are not standalone pages', 'compareoxygen'); ?></h2>
            </div>
            <div class="prose">
                <p><?php esc_html_e('Any two published models can be compared in the tool. We publish a standalone page only when an editor has something specific to add: a meaningful trade-off, a documented asymmetry in the specifications, or a pairing readers repeatedly ask about.', 'compareoxygen'); ?></p>
                <p><?php esc_html_e('Generated permutations are marked noindex. That keeps thin, near-duplicate pages out of search results without limiting what readers can do with the tool.', 'compareoxygen'); ?></p>
                <h3><?php esc_html_e('What every comparison page includes', 'compareoxygen'); ?></h3>
                <ul>
                    <li><?php esc_html_e('Both model records, with their manufacturer sources and verification dates.', 'compareoxygen'); ?></li>
                    <li><?php esc_html_e('Differences highlighted as a reading aid, never as a recommendation.', 'compareoxygen'); ?></li>
                    <li><?php esc_html_e('A visible “Not confirmed” state wherever a manufacturer does not publish a figure.', 'compareoxygen'); ?></li>
                </ul>
            </div>
            <?php compareoxygen_medical_note(); ?>
        </div>
    </section>

    <div class="container">
        <?php
        compareoxygen_cta_band(
            __('Pick a pair from the review library', 'compareoxygen'),
            __('Every reviewed model links into the comparison tool. Choose two and read the difference count before you read the marketing.', 'compareoxygen'),
            __('Browse model reviews', 'compareoxygen'),
            get_post_type_archive_link('poc_model') ?: home_url('/reviews/'),
            __('Compare now', 'compareoxygen'),
            coc_compare_tool_url()
        );
        ?>
    </div>
</main>
<?php
get_footer();
