<?php
/**
 * Site header.
 *
 * @package compareoxygen
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="https://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main-content"><?php esc_html_e('Skip to main content', 'compareoxygen'); ?></a>

<aside class="utility-bar" aria-label="<?php esc_attr_e('Research sourcing and update information', 'compareoxygen'); ?>">
    <div class="container utility-bar__inner">
        <p><?php echo compareoxygen_icon('shield'); ?><?php echo esc_html(coc_opt('utility_text')); ?></p>
        <p>
            <a href="<?php echo esc_url(compareoxygen_page_url('how-we-rate')); ?>"><?php esc_html_e('How we rate', 'compareoxygen'); ?></a>
            <span aria-hidden="true">·</span>
            <span><?php printf(esc_html__('Updated %s', 'compareoxygen'), esc_html(compareoxygen_last_updated())); ?></span>
        </p>
    </div>
</aside>

<header class="site-header" data-site-header>
    <div class="container header-inner">
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

        <nav class="primary-nav" id="primary-nav" aria-label="<?php esc_attr_e('Primary navigation', 'compareoxygen'); ?>" data-primary-nav>
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container' => false,
                'menu_class' => 'nav-menu',
                'depth' => 2,
                'fallback_cb' => 'compareoxygen_primary_fallback',
            ]);
            ?>
        </nav>

        <div class="header-actions">
            <button class="icon-btn" type="button" aria-expanded="false" aria-controls="site-search" data-search-toggle>
                <?php echo compareoxygen_icon('search'); ?>
                <span class="screen-reader-text"><?php esc_html_e('Search the site', 'compareoxygen'); ?></span>
            </button>
            <a class="btn btn--primary btn--sm header-compare" href="<?php echo esc_url(coc_compare_tool_url()); ?>"><?php echo compareoxygen_icon('scale'); ?><?php echo esc_html(coc_opt('header_cta_label')); ?></a>
            <button class="icon-btn menu-btn" type="button" aria-expanded="false" aria-controls="primary-nav" data-menu-toggle>
                <span data-menu-icon><?php echo compareoxygen_icon('menu'); ?></span>
                <span class="screen-reader-text"><?php esc_html_e('Open navigation menu', 'compareoxygen'); ?></span>
            </button>
        </div>
    </div>

    <div class="search-panel" id="site-search" data-search-panel hidden>
        <div class="container">
            <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <label for="site-search-input" class="screen-reader-text"><?php esc_html_e('Search reviews, comparisons, and guides', 'compareoxygen'); ?></label>
                <input id="site-search-input" type="search" name="s" placeholder="<?php esc_attr_e('Search reviews, comparisons, and guides', 'compareoxygen'); ?>" value="<?php echo esc_attr(get_search_query()); ?>">
                <button class="btn btn--primary" type="submit"><?php echo compareoxygen_icon('search'); ?><?php esc_html_e('Search', 'compareoxygen'); ?></button>
            </form>
        </div>
    </div>
</header>
