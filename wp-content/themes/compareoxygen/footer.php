<?php
/**
 * Site footer.
 *
 * @package compareoxygen
 */
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-main">
            <div class="footer-brand">
                <a class="brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(sprintf(__('%s — home', 'compareoxygen'), get_bloginfo('name'))); ?>">
                    <span class="brand__mark" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round">
                            <circle cx="9" cy="12" r="4.6"/>
                            <circle cx="16.4" cy="8.2" r="2.9"/>
                            <path d="M2.6 17.4c1.7 1.3 3.2 1.3 4.9 0s3.2-1.3 4.9 0 3.2 1.3 4.9 0 3.2-1.3 4.1-.6"/>
                        </svg>
                    </span>
                    <span class="brand__text">
                        <span class="brand__name">Compare<em>Oxygen</em></span>
                        <span class="brand__tag"><?php esc_html_e('Concentrator research', 'compareoxygen'); ?></span>
                    </span>
                </a>
                <p class="footer-mission"><?php echo esc_html(coc_opt('footer_mission')); ?></p>
                <div class="footer-badges">
                    <span class="footer-badge"><?php echo compareoxygen_icon('check'); ?><?php echo esc_html(coc_opt('footer_badge_1')); ?></span>
                    <span class="footer-badge"><?php echo compareoxygen_icon('shield'); ?><?php echo esc_html(coc_opt('footer_badge_2')); ?></span>
                </div>
            </div>

            <div class="footer-col">
                <h2><?php esc_html_e('Compare', 'compareoxygen'); ?></h2>
                <ul>
                    <li><a href="<?php echo esc_url(coc_compare_tool_url()); ?>"><?php esc_html_e('Comparison tool', 'compareoxygen'); ?></a></li>
                    <li><a href="<?php echo esc_url(compareoxygen_page_url('best', '/best/portable-oxygen-concentrators/')); ?>"><?php esc_html_e('Best POCs', 'compareoxygen'); ?></a></li>
                    <li><a href="<?php echo esc_url(get_post_type_archive_link('poc_comparison') ?: home_url('/comparisons/')); ?>"><?php esc_html_e('Comparison library', 'compareoxygen'); ?></a></li>
                    <li><a href="<?php echo esc_url(get_post_type_archive_link('poc_model') ?: home_url('/reviews/')); ?>"><?php esc_html_e('Model reviews', 'compareoxygen'); ?></a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h2><?php esc_html_e('Research', 'compareoxygen'); ?></h2>
                <ul>
                    <li><a href="<?php echo esc_url(compareoxygen_page_url('guides')); ?>"><?php esc_html_e('Buying guides', 'compareoxygen'); ?></a></li>
                    <li><a href="<?php echo esc_url(compareoxygen_page_url('brands')); ?>"><?php esc_html_e('Brands', 'compareoxygen'); ?></a></li>
                    <li><a href="<?php echo esc_url(compareoxygen_page_url('how-we-rate')); ?>"><?php esc_html_e('How we rate', 'compareoxygen'); ?></a></li>
                    <li><a href="<?php echo esc_url(compareoxygen_page_url('editorial-policy')); ?>"><?php esc_html_e('Editorial policy', 'compareoxygen'); ?></a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h2><?php esc_html_e('Company', 'compareoxygen'); ?></h2>
                <ul>
                    <li><a href="<?php echo esc_url(compareoxygen_page_url('about')); ?>"><?php esc_html_e('About us', 'compareoxygen'); ?></a></li>
                    <li><a href="<?php echo esc_url(compareoxygen_page_url('contact')); ?>"><?php esc_html_e('Contact', 'compareoxygen'); ?></a></li>
                    <li><a href="<?php echo esc_url(compareoxygen_page_url('disclosure')); ?>"><?php esc_html_e('Affiliate disclosure', 'compareoxygen'); ?></a></li>
                    <li><a href="<?php echo esc_url(compareoxygen_page_url('accessibility')); ?>"><?php esc_html_e('Accessibility', 'compareoxygen'); ?></a></li>
                </ul>
            </div>
        </div>

        <?php if (is_active_sidebar('coc-footer-widgets')) : ?>
            <div class="footer-widgets">
                <?php dynamic_sidebar('coc-footer-widgets'); ?>
            </div>
        <?php endif; ?>

        <div class="footer-legal">
            <p class="footer-disclaimer">
                <?php echo esc_html(coc_opt('footer_disclaimer')); ?>
            </p>
            <div class="footer-bottom">
                <p class="mb-0">&copy; <?php echo esc_html(date_i18n('Y')); ?> CompareOxygenConcentrators.com. <?php echo esc_html(coc_opt('footer_copyright')); ?></p>
                <ul>
                    <li><a href="<?php echo esc_url(compareoxygen_page_url('privacy-policy')); ?>"><?php esc_html_e('Privacy policy', 'compareoxygen'); ?></a></li>
                    <li><a href="<?php echo esc_url(compareoxygen_page_url('terms')); ?>"><?php esc_html_e('Terms of use', 'compareoxygen'); ?></a></li>
                    <li><a href="<?php echo esc_url(compareoxygen_page_url('disclosure')); ?>"><?php esc_html_e('Disclosure', 'compareoxygen'); ?></a></li>
                    <li><a href="<?php echo esc_url(compareoxygen_page_url('contact')); ?>"><?php esc_html_e('Contact', 'compareoxygen'); ?></a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
