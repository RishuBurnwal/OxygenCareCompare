<?php
/**
 * Compare Oxygen theme setup, assets, and presentation helpers.
 *
 * @package compareoxygen
 */
if (!defined('ABSPATH')) { exit; }

define('COMPAREOXYGEN_VERSION', '1.0.0');

require_once get_template_directory() . '/inc/site-settings.php';

function compareoxygen_theme_setup(): void {
    load_theme_textdomain('compareoxygen', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    add_theme_support('custom-logo', ['height' => 64, 'width' => 280, 'flex-height' => true, 'flex-width' => true]);
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_image_size('coc-card', 720, 540, false);
    add_image_size('coc-wide', 1200, 800, false);
    register_nav_menus([
        'primary' => __('Primary navigation', 'compareoxygen'),
        'footer_compare' => __('Footer: compare', 'compareoxygen'),
        'footer_research' => __('Footer: research', 'compareoxygen'),
        'footer_editorial' => __('Footer: editorial', 'compareoxygen'),
    ]);
}
add_action('after_setup_theme', 'compareoxygen_theme_setup');

function compareoxygen_enqueue_assets(): void {
    $dir = get_template_directory();
    $uri = get_template_directory_uri();
    $style_version = (string) filemtime($dir . '/style.css');
    wp_enqueue_style('compareoxygen-style', get_stylesheet_uri(), [], $style_version);
    wp_enqueue_script('compareoxygen-app', $uri . '/assets/js/app.js', [], (string) filemtime($dir . '/assets/js/app.js'), true);
    wp_script_add_data('compareoxygen-app', 'defer', true);
}
add_action('wp_enqueue_scripts', 'compareoxygen_enqueue_assets');

function compareoxygen_favicon(): void {
    $icon = get_template_directory_uri() . '/assets/img/favicon.svg';
    printf('<link rel="icon" href="%s" type="image/svg+xml">' . "\n", esc_url($icon));
    printf('<link rel="alternate icon" href="%s" type="image/x-icon">' . "\n", esc_url(get_template_directory_uri() . '/assets/img/favicon.ico'));
}
add_action('wp_head', 'compareoxygen_favicon', 5);

function compareoxygen_clean_head(): void {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
}
add_action('init', 'compareoxygen_clean_head');

/** Resolve a page URL by slug with a safe fallback. */
function compareoxygen_page_url(string $slug, string $fallback = '/'): string {
    $page = get_page_by_path($slug);
    return $page instanceof WP_Post ? get_permalink($page) : home_url($fallback);
}

function compareoxygen_icon(string $name, string $class = ''): string {
    $icons = [
        'arrow-right' => '<path d="M4 12h15m0 0-5.5-5.5M19 12l-5.5 5.5"/>',
        'arrow-up-right' => '<path d="M7 17 17 7M9 7h8v8"/>',
        'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
        'search' => '<circle cx="11" cy="11" r="6.5"/><path d="m16 16 4 4"/>',
        'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'close' => '<path d="M6 6l12 12M18 6 6 18"/>',
        'check' => '<path d="m5 12.5 4.5 4.5L19 7"/>',
        'minus' => '<path d="M6 12h12"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'scale' => '<path d="M12 4v16M6 8h12M6 8 3 15h6L6 8Zm12 0-3 7h6l-3-7ZM8 20h8"/>',
        'battery' => '<rect x="3" y="8" width="16" height="8" rx="2"/><path d="M21 11v2M6 11v2M9 11v2"/>',
        'plane' => '<path d="M10 21v-5l-6-4V9l6 2V6l-2-2V3l4 1 4-1v1l-2 2v5l6-2v3l-6 4v5l2 2v1l-4-1-4 1v-1l2-2Z"/>',
        'shield' => '<path d="M12 3 5 6v5c0 4.5 3 8 7 10 4-2 7-5.5 7-10V6l-7-3Z"/><path d="m9 12 2 2 4-4"/>',
        'book' => '<path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v15H6.5A2.5 2.5 0 0 0 4 20.5v-15Z"/><path d="M4 20.5A2.5 2.5 0 0 1 6.5 18H20v3H6.5A2.5 2.5 0 0 1 4 20.5Z"/>',
        'weight' => '<path d="M6 8h12l2 12H4L6 8Z"/><circle cx="12" cy="5" r="2.5"/>',
        'volume' => '<path d="M5 9v6h4l5 4V5L9 9H5Z"/><path d="M17 9a4 4 0 0 1 0 6"/>',
        'plug' => '<path d="M9 3v5M15 3v5M6 8h12v3a6 6 0 0 1-12 0V8ZM12 17v4"/>',
        'route' => '<circle cx="6" cy="6" r="2.5"/><circle cx="18" cy="18" r="2.5"/><path d="M6 8.5v3A3.5 3.5 0 0 0 9.5 15H15a3 3 0 0 1 0 3"/>',
        'info' => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/>',
        'alert' => '<path d="M12 3 2.5 20h19L12 3Z"/><path d="M12 9v5M12 17h.01"/>',
        'clock' => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7v5l3.5 2"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/>',
        'spark' => '<path d="M12 3v4M12 17v4M3 12h4M17 12h4M6.5 6.5l2.8 2.8M14.7 14.7l2.8 2.8M17.5 6.5l-2.8 2.8M9.3 14.7l-2.8 2.8"/>',
        'layers' => '<path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 13 9 5 9-5"/>',
        'file' => '<path d="M6 3h8l4 4v14H6V3Z"/><path d="M14 3v4h4M9 12h6M9 16h6"/>',
        'list' => '<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>',
    ];
    $path = $icons[$name] ?? $icons['info'];
    return sprintf(
        '<svg class="icon %s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%s</svg>',
        esc_attr($class),
        $path
    );
}

/** Breadcrumbs with BreadcrumbList microdata. */
function compareoxygen_breadcrumbs(): void {
    if (is_front_page()) { return; }
    $items = [['label' => __('Home', 'compareoxygen'), 'url' => home_url('/')]];
    if (is_singular('poc_model')) {
        $items[] = ['label' => __('Reviews', 'compareoxygen'), 'url' => get_post_type_archive_link('poc_model')];
        $items[] = ['label' => get_the_title(), 'url' => ''];
    } elseif (is_singular('poc_comparison')) {
        $items[] = ['label' => __('Comparisons', 'compareoxygen'), 'url' => get_post_type_archive_link('poc_comparison')];
        $items[] = ['label' => get_the_title(), 'url' => ''];
    } elseif (is_tax('poc_brand')) {
        $items[] = ['label' => __('Brands', 'compareoxygen'), 'url' => compareoxygen_page_url('brands')];
        $items[] = ['label' => single_term_title('', false), 'url' => ''];
    } elseif (is_post_type_archive('poc_model')) {
        $items[] = ['label' => __('Reviews', 'compareoxygen'), 'url' => ''];
    } elseif (is_post_type_archive('poc_comparison')) {
        $items[] = ['label' => __('Comparisons', 'compareoxygen'), 'url' => ''];
    } elseif (is_search()) {
        $items[] = ['label' => __('Search', 'compareoxygen'), 'url' => ''];
    } elseif (is_404()) {
        $items[] = ['label' => __('Page not found', 'compareoxygen'), 'url' => ''];
    } elseif (is_singular()) {
        $post = get_queried_object();
        if ($post instanceof WP_Post && $post->post_type === 'post') {
            $items[] = ['label' => __('Guides', 'compareoxygen'), 'url' => compareoxygen_page_url('guides')];
        }
        $items[] = ['label' => get_the_title(), 'url' => ''];
    }
    echo '<nav class="breadcrumbs" aria-label="' . esc_attr__('Breadcrumb', 'compareoxygen') . '"><div class="container"><ol itemscope itemtype="https://schema.org/BreadcrumbList">';
    foreach ($items as $index => $item) {
        $position = $index + 1;
        echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        if ($item['url']) {
            printf('<a itemprop="item" href="%s"><span itemprop="name">%s</span></a>', esc_url($item['url']), esc_html($item['label']));
        } else {
            printf('<span itemprop="name" aria-current="page">%s</span>', esc_html($item['label']));
        }
        printf('<meta itemprop="position" content="%d"></li>', $position);
    }
    echo '</ol></div></nav>';
}

function compareoxygen_section_head(string $eyebrow, string $title, string $intro = '', string $align = 'start', string $level = 'h2'): void {
    printf('<header class="section-head section-head--%s">', esc_attr($align));
    if ($eyebrow !== '') { printf('<p class="eyebrow">%s</p>', esc_html($eyebrow)); }
    printf('<%1$s class="section-title">%2$s</%1$s>', tag_escape($level), esc_html($title));
    if ($intro !== '') { printf('<p class="section-intro">%s</p>', esc_html($intro)); }
    echo '</header>';
}

/** Editorial heading with inline markup allowed. */
function compareoxygen_heading(string $text, string $class = 'section-title', string $tag = 'h2'): void {
    printf('<%1$s class="%2$s">%3$s</%1$s>', tag_escape($tag), esc_attr($class), wp_kses_post($text));
}

/** Weighted reading time in minutes. */
function compareoxygen_reading_time(string $content): int {
    $words = str_word_count(wp_strip_all_tags($content));
    return max(1, (int) ceil($words / 220));
}

function compareoxygen_format_date(string $date): string {
    if (!$date) { return ''; }
    $timestamp = strtotime($date);
    return $timestamp ? date_i18n('F j, Y', $timestamp) : $date;
}

/** Most recent modification date across published research content. */
function compareoxygen_last_updated(): string {
    static $label = null;
    if ($label !== null) { return $label; }
    $ids = get_posts([
        'post_type' => ['post', 'page', 'poc_model', 'poc_comparison'],
        'post_status' => 'publish',
        'numberposts' => 1,
        'orderby' => 'modified',
        'order' => 'DESC',
        'fields' => 'ids',
        'no_found_rows' => true,
    ]);
    $timestamp = $ids ? (int) get_post_modified_time('U', false, $ids[0]) : time();
    $label = date_i18n('F j, Y', $timestamp);
    return $label;
}

/** Add ids to h2 headings and return a TOC. */
function compareoxygen_prepare_content(string $content): array {
    $toc = [];
    $used = [];
    $content = preg_replace_callback('/<h2([^>]*)>(.*?)<\/h2>/is', static function (array $match) use (&$toc, &$used): string {
        $title = trim(wp_strip_all_tags($match[2]));
        if ($title === '') { return $match[0]; }
        $slug = sanitize_title($title) ?: 'section';
        $base = $slug;
        $i = 2;
        while (isset($used[$slug])) { $slug = $base . '-' . $i; $i++; }
        $used[$slug] = true;
        $toc[] = ['id' => $slug, 'title' => $title];
        $attrs = $match[1];
        if (stripos($attrs, 'id=') === false) {
            $attrs .= ' id="' . esc_attr($slug) . '"';
        }
        return '<h2' . $attrs . '>' . $match[2] . '</h2>';
    }, $content);
    return ['content' => (string) $content, 'toc' => $toc];
}

function compareoxygen_faq(array $items): void {
    if (!$items) { return; }
    echo '<div class="faq">';
    foreach ($items as $question => $answer) {
        printf(
            '<details class="faq-item"><summary><span>%s</span>%s</summary><div class="faq-answer"><p>%s</p></div></details>',
            esc_html($question),
            compareoxygen_icon('chevron-down', 'faq-chevron'),
            wp_kses_post($answer)
        );
    }
    echo '</div>';
}

function compareoxygen_faq_schema(array $items): void {
    if (!$items) { return; }
    $entities = [];
    foreach ($items as $question => $answer) {
        $entities[] = [
            '@type' => 'Question',
            'name' => $question,
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => wp_strip_all_tags($answer)],
        ];
    }
    echo '<script type="application/ld+json">' . wp_json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $entities,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}

/** Reusable conclusion CTA band. */
function compareoxygen_cta_band(string $title, string $text, string $primary_label, string $primary_url, string $secondary_label = '', string $secondary_url = ''): void {
    ?>
    <section class="cta-band">
        <div class="cta-band__inner">
            <div class="cta-band__copy">
                <p class="eyebrow eyebrow--light"><?php esc_html_e('Next step', 'compareoxygen'); ?></p>
                <h2><?php echo esc_html($title); ?></h2>
                <p><?php echo esc_html($text); ?></p>
            </div>
            <div class="cta-band__actions">
                <a class="btn btn--onDark" href="<?php echo esc_url($primary_url); ?>"><?php echo esc_html($primary_label); ?><?php echo compareoxygen_icon('arrow-right'); ?></a>
                <?php if ($secondary_label && $secondary_url) : ?>
                    <a class="btn btn--ghostLight" href="<?php echo esc_url($secondary_url); ?>"><?php echo esc_html($secondary_label); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php
}

function compareoxygen_disclosure(string $extra = ''): void {
    ?>
    <p class="disclosure">
        <?php echo compareoxygen_icon('info'); ?>
        <span>
            <strong><?php esc_html_e('How this page is funded:', 'compareoxygen'); ?></strong>
            <?php esc_html_e('CompareOxygenConcentrators.com is owned by Oxygen Care Supply. We may earn revenue when readers choose a product through our links. Research findings and specifications are not edited, ranked, or priced in exchange for compensation.', 'compareoxygen'); ?>
            <?php if ($extra) { echo ' ' . esc_html($extra); } ?>
            <a href="<?php echo esc_url(compareoxygen_page_url('disclosure')); ?>"><?php esc_html_e('Read the full disclosure', 'compareoxygen'); ?></a>
        </span>
    </p>
    <?php
}

function compareoxygen_medical_note(string $class = ''): void {
    ?>
    <aside class="medical-note <?php echo esc_attr($class); ?>">
        <?php echo compareoxygen_icon('shield'); ?>
        <div>
            <h2><?php esc_html_e('Medical information limitation', 'compareoxygen'); ?></h2>
            <p><?php esc_html_e('This website provides general educational information and product research. It is not medical advice, and it does not determine whether a concentrator is clinically appropriate for a specific person. Oxygen flow settings, prescriptions, and travel requirements must be confirmed with a qualified clinician and your oxygen supplier.', 'compareoxygen'); ?></p>
        </div>
    </aside>
    <?php
}

/** Technical illustration of a portable concentrator (no manufacturer branding). */
function compareoxygen_device_illustration(string $variant = 'standard', string $accent = 'teal', string $class = ''): void {
    $is_compact = $variant === 'compact';
    $w = $is_compact ? 118 : 148;
    $h = $is_compact ? 196 : 172;
    $x = $is_compact ? 41 : 26;
    $y = 34;
    $accent_colors = ['teal' => '#087F79', 'navy' => '#123B55', 'mint' => '#24A69A'];
    $stroke = $accent_colors[$accent] ?? $accent_colors['teal'];
    $uid = 'dev-' . wp_unique_id();
    $glow = 'glow-' . wp_unique_id();
    ?>
    <svg class="device-art <?php echo esc_attr($class); ?>" viewBox="0 0 200 260" role="img" aria-label="<?php esc_attr_e('Technical illustration of a portable oxygen concentrator. This is a generic illustration, not a manufacturer photograph.', 'compareoxygen'); ?>" focusable="false">
        <defs>
            <linearGradient id="<?php echo esc_attr($uid); ?>" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0" stop-color="#FFFFFF"/>
                <stop offset="1" stop-color="#EEF5F6"/>
            </linearGradient>
            <radialGradient id="<?php echo esc_attr($glow); ?>" cx="50%" cy="42%" r="55%">
                <stop offset="0" stop-color="#FFFFFF" stop-opacity=".9"/>
                <stop offset="1" stop-color="#E8F5F3" stop-opacity="0"/>
            </radialGradient>
        </defs>
        <circle cx="100" cy="118" r="94" fill="url(#<?php echo esc_attr($glow); ?>)"/>
        <ellipse cx="100" cy="222" rx="<?php echo (int) ($w / 1.5); ?>" ry="12" fill="#123B55" opacity=".10"/>
        <g>
            <rect x="<?php echo (int) $x; ?>" y="<?php echo (int) $y; ?>" width="<?php echo (int) $w; ?>" height="<?php echo (int) $h; ?>" rx="22" fill="url(#<?php echo esc_attr($uid); ?>)" stroke="#C9D8DE" stroke-width="2"/>
            <rect x="<?php echo (int) ($x + 14); ?>" y="<?php echo (int) ($y + 16); ?>" width="<?php echo (int) ($w - 28); ?>" height="42" rx="9" fill="#123B55"/>
            <rect x="<?php echo (int) ($x + 22); ?>" y="<?php echo (int) ($y + 26); ?>" width="<?php echo (int) ($w - 60); ?>" height="7" rx="3.5" fill="#7FE0D4" opacity=".9"/>
            <rect x="<?php echo (int) ($x + 22); ?>" y="<?php echo (int) ($y + 40); ?>" width="<?php echo (int) ($w - 70); ?>" height="5" rx="2.5" fill="#FFFFFF" opacity=".55"/>
            <circle cx="<?php echo (int) ($x + $w - 30); ?>" cy="<?php echo (int) ($y + 37); ?>" r="5" fill="#24A69A"/>
            <?php for ($i = 0; $i < 5; $i++) : ?>
                <rect x="<?php echo (int) ($x + 18); ?>" y="<?php echo (int) ($y + 76 + ($i * 13)); ?>" width="<?php echo (int) ($w - 52); ?>" height="4" rx="2" fill="#DCE5E9"/>
            <?php endfor; ?>
            <circle cx="<?php echo (int) ($x + 34); ?>" cy="<?php echo (int) ($y + $h - 34); ?>" r="12" fill="none" stroke="<?php echo esc_attr($stroke); ?>" stroke-width="4"/>
            <circle cx="<?php echo (int) ($x + 34); ?>" cy="<?php echo (int) ($y + $h - 34); ?>" r="4" fill="<?php echo esc_attr($stroke); ?>"/>
            <rect x="<?php echo (int) ($x + 58); ?>" y="<?php echo (int) ($y + $h - 41); ?>" width="<?php echo (int) ($w - 76); ?>" height="14" rx="7" fill="#E8F5F3" stroke="#CFE3E0"/>
            <path d="M<?php echo (int) ($x + $w / 2); ?> <?php echo (int) $y; ?> v-14 a10 10 0 0 1 10 -10 h14" fill="none" stroke="<?php echo esc_attr($stroke); ?>" stroke-width="4" stroke-linecap="round"/>
        </g>
    </svg>
    <?php
}

/** Product card shared across the site. */
function compareoxygen_product_card(int $model_id, array $args = []): void {
    $args = wp_parse_args($args, ['variant' => 'default', 'show_summary' => true]);
    $post = get_post($model_id);
    if (!$post instanceof WP_Post) { return; }
    $weight = coc_spec($model_id, 'weight')['display'];
    $battery = coc_spec($model_id, 'battery_extended')['display'] ?: coc_spec($model_id, 'battery_standard')['display'];
    $settings = coc_spec($model_id, 'settings')['display'];
    $noise = coc_spec($model_id, 'sound_level')['display'];
    $brand = coc_brand_of($model_id);
    $award = coc_award_label($model_id);
    $verified = coc_verified_count($model_id);
    $compare_target = coc_compare_tool_url();
    ?>
    <article class="product-card">
        <div class="product-card__media">
            <?php if ($award) : ?>
                <span class="badge badge--award"><?php echo compareoxygen_icon('spark'); ?><?php echo esc_html($award); ?></span>
            <?php endif; ?>
            <a class="product-card__media-link" href="<?php echo esc_url(get_permalink($model_id)); ?>" tabindex="-1" aria-hidden="true">
                <span class="product-card__stage">
                    <?php compareoxygen_device_illustration('standard', $verified > 14 ? 'navy' : 'teal', 'product-card__art'); ?>
                </span>
            </a>
            <p class="illustration-note"><?php esc_html_e('Technical illustration', 'compareoxygen'); ?></p>
        </div>
        <div class="product-card__body">
            <p class="product-card__brand"><?php echo esc_html($brand); ?></p>
            <h3 class="product-card__title"><a href="<?php echo esc_url(get_permalink($model_id)); ?>"><?php echo esc_html(get_the_title($model_id)); ?></a></h3>
            <?php if ($args['show_summary']) : ?>
                <p class="product-card__summary"><?php echo esc_html(wp_trim_words(get_the_excerpt($model_id), 20)); ?></p>
            <?php endif; ?>
            <dl class="mini-specs">
                <div><dt><?php esc_html_e('Weight', 'compareoxygen'); ?></dt><dd class="num"><?php echo $weight ? esc_html($weight) : '<span class="unconfirmed">' . esc_html__('Not confirmed', 'compareoxygen') . '</span>'; ?></dd></div>
                <div><dt><?php esc_html_e('Settings', 'compareoxygen'); ?></dt><dd class="num"><?php echo $settings ? esc_html($settings) : '<span class="unconfirmed">' . esc_html__('Not confirmed', 'compareoxygen') . '</span>'; ?></dd></div>
                <div><dt><?php esc_html_e('Battery', 'compareoxygen'); ?></dt><dd class="num"><?php echo $battery ? esc_html($battery) : '<span class="unconfirmed">' . esc_html__('Not confirmed', 'compareoxygen') . '</span>'; ?></dd></div>
                <div><dt><?php esc_html_e('Noise', 'compareoxygen'); ?></dt><dd class="num"><?php echo $noise ? esc_html($noise) : '<span class="unconfirmed">' . esc_html__('Not confirmed', 'compareoxygen') . '</span>'; ?></dd></div>
            </dl>
            <div class="product-card__meta">
                <span class="verified-count"><?php echo compareoxygen_icon('check'); ?><?php printf(esc_html__('%d verified facts', 'compareoxygen'), (int) $verified); ?></span>
            </div>
            <div class="product-card__actions">
                <a class="btn btn--primary btn--sm" href="<?php echo esc_url(get_permalink($model_id)); ?>"><?php esc_html_e('Read review', 'compareoxygen'); ?></a>
                <a class="btn btn--secondary btn--sm" href="<?php echo esc_url(add_query_arg(['first' => $model_id], $compare_target)); ?>" data-compare-anchor><?php esc_html_e('Compare', 'compareoxygen'); ?></a>
            </div>
        </div>
    </article>
    <?php
}

/** Compact review row used in the featured reviews section. */
function compareoxygen_review_row(int $model_id): void {
    $weight = coc_spec($model_id, 'weight')['display'];
    $settings = coc_spec($model_id, 'settings')['display'];
    $battery = coc_spec($model_id, 'battery_extended')['display'] ?: coc_spec($model_id, 'battery_standard')['display'];
    ?>
    <article class="review-row">
        <div class="review-row__media"><?php compareoxygen_device_illustration('compact', 'teal', 'review-row__art'); ?></div>
        <div class="review-row__body">
            <p class="product-card__brand"><?php echo esc_html(coc_brand_of($model_id)); ?></p>
            <h3><a href="<?php echo esc_url(get_permalink($model_id)); ?>"><?php echo esc_html(get_the_title($model_id)); ?></a></h3>
            <p><?php echo esc_html(wp_trim_words(get_the_excerpt($model_id), 22)); ?></p>
            <ul class="inline-specs">
                <?php if ($weight) : ?><li><?php echo compareoxygen_icon('weight'); ?><span><?php echo esc_html($weight); ?></span></li><?php endif; ?>
                <?php if ($settings) : ?><li><?php echo compareoxygen_icon('layers'); ?><span><?php printf(esc_html__('Settings %s', 'compareoxygen'), esc_html($settings)); ?></span></li><?php endif; ?>
                <?php if ($battery) : ?><li><?php echo compareoxygen_icon('battery'); ?><span><?php echo esc_html($battery); ?></span></li><?php endif; ?>
            </ul>
        </div>
        <div class="review-row__action">
            <a class="link-arrow" href="<?php echo esc_url(get_permalink($model_id)); ?>"><?php esc_html_e('Read the review', 'compareoxygen'); ?><?php echo compareoxygen_icon('arrow-right'); ?></a>
        </div>
    </article>
    <?php
}

/** Brand monogram tile (no third-party logo files supplied). */
function compareoxygen_brand_mark(string $name, string $size = 'md'): void {
    $initials = '';
    foreach (preg_split('/\s+/', trim($name)) as $word) {
        if ($word !== '') { $initials .= strtoupper(substr($word, 0, 1)); }
        if (strlen($initials) >= 2) { break; }
    }
    printf('<span class="brand-mark brand-mark--%s" aria-hidden="true">%s</span>', esc_attr($size), esc_html($initials ?: 'OC'));
}

/** Primary nav fallback when no menu is assigned. */
function compareoxygen_primary_fallback(): void {
    $links = [
        ['label' => __('Compare', 'compareoxygen'), 'url' => compareoxygen_page_url('compare')],
        ['label' => __('Best POCs', 'compareoxygen'), 'url' => compareoxygen_page_url('best', '/best/portable-oxygen-concentrators/')],
        ['label' => __('Reviews', 'compareoxygen'), 'url' => get_post_type_archive_link('poc_model') ?: home_url('/reviews/')],
        ['label' => __('Comparisons', 'compareoxygen'), 'url' => get_post_type_archive_link('poc_comparison') ?: home_url('/comparisons/')],
        ['label' => __('Guides', 'compareoxygen'), 'url' => compareoxygen_page_url('guides')],
        ['label' => __('Brands', 'compareoxygen'), 'url' => compareoxygen_page_url('brands')],
        ['label' => __('About', 'compareoxygen'), 'url' => compareoxygen_page_url('about')],
    ];
    echo '<ul class="nav-menu">';
    foreach ($links as $link) {
        printf('<li class="menu-item"><a href="%s">%s</a></li>', esc_url($link['url']), esc_html($link['label']));
    }
    echo '</ul>';
}

/** Desktop + mobile table of contents from an item list. */
function compareoxygen_toc(array $items, string $cta_url = '', string $cta_label = ''): void {
    if (!$items) { return; }
    $list = static function () use ($items): void {
        echo '<ol>';
        foreach ($items as $item) {
            printf('<li><a href="#%s">%s</a></li>', esc_attr($item['id']), esc_html($item['title']));
        }
        echo '</ol>';
    };
    ?>
    <aside class="toc" aria-label="<?php esc_attr_e('On this page', 'compareoxygen'); ?>">
        <p class="toc__title"><?php echo compareoxygen_icon('list'); ?><?php esc_html_e('On this page', 'compareoxygen'); ?></p>
        <?php $list(); ?>
        <?php if ($cta_url && $cta_label) : ?>
            <div class="toc__cta">
                <p><?php esc_html_e('Still deciding?', 'compareoxygen'); ?></p>
                <a class="btn btn--primary btn--sm btn--block" href="<?php echo esc_url($cta_url); ?>"><?php echo esc_html($cta_label); ?><?php echo compareoxygen_icon('arrow-right'); ?></a>
            </div>
        <?php endif; ?>
    </aside>
    <div class="toc-mobile">
        <details>
            <summary><?php esc_html_e('On this page', 'compareoxygen'); ?><?php echo compareoxygen_icon('chevron-down'); ?></summary>
            <?php $list(); ?>
        </details>
    </div>
    <?php
}

/** Every source cited on a model record, grouped by URL. */
function compareoxygen_sources(int $model_id): void {
    $sources = [];
    foreach (COC_Core::SPECS as $key => $meta) {
        $spec = coc_spec($model_id, $key);
        if (!$spec['complete']) { continue; }
        $url = $spec['source'];
        if (!isset($sources[$url])) {
            $sources[$url] = ['facts' => [], 'verified' => ''];
        }
        $sources[$url]['facts'][] = $meta['label'];
        if ($spec['verified'] > $sources[$url]['verified']) {
            $sources[$url]['verified'] = $spec['verified'];
        }
    }
    if (!$sources) {
        echo '<div class="empty-state"><h2>' . esc_html__('No verified sources yet', 'compareoxygen') . '</h2><p>' . esc_html__('This record is published without confirmed specifications. Nothing is shown publicly until a manufacturer source and verification date are recorded.', 'compareoxygen') . '</p></div>';
        return;
    }
    echo '<ul class="sources-list">';
    foreach ($sources as $url => $source) {
        $host = (string) wp_parse_url($url, PHP_URL_HOST);
        $host = preg_replace('/^www\./', '', $host);
        echo '<li class="source-row">';
        echo '<span class="source-row__icon">' . compareoxygen_icon('file') . '</span>';
        echo '<div>';
        echo '<a class="source-row__link" href="' . esc_url($url) . '" target="_blank" rel="nofollow noopener">' . esc_html($host) . compareoxygen_icon('arrow-up-right') . '</a>';
        echo '<p class="source-row__facts">' . esc_html(implode(' · ', $source['facts'])) . '</p>';
        echo '<span class="source-row__date">' . sprintf(esc_html__('Verified %s', 'compareoxygen'), esc_html(compareoxygen_format_date($source['verified']))) . '</span>';
        echo '</div></li>';
    }
    echo '</ul>';
}

/** Contact form handler: validates, stores, and attempts notification. */
function compareoxygen_handle_contact(): void {
    $fallback = compareoxygen_page_url('contact', '/contact/');
    $redirect = wp_get_referer() ?: $fallback;
    $redirect = remove_query_arg(['contact'], $redirect);

    $fail = static function (string $code) use ($redirect): void {
        wp_safe_redirect(add_query_arg('contact', $code, $redirect) . '#contact-form');
        exit;
    };

    if (!isset($_POST['coc_contact_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['coc_contact_nonce'])), 'coc_contact')) {
        $fail('error');
    }

    // Honeypot filled: pretend success, store nothing.
    if (!empty($_POST['website'])) {
        wp_safe_redirect(add_query_arg('contact', 'success', $redirect) . '#contact-form');
        exit;
    }

    $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $subject = isset($_POST['subject']) ? sanitize_text_field(wp_unslash($_POST['subject'])) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';

    if ($name === '' || !is_email($email) || $message === '' || mb_strlen($message) < 20) {
        $fail('invalid');
    }

    $allowed_subjects = ['correction', 'general', 'accessibility', 'partnership'];
    if (!in_array($subject, $allowed_subjects, true)) { $subject = 'general'; }

    $entry = [
        'name' => $name,
        'email' => $email,
        'subject' => $subject,
        'message' => $message,
        'received' => current_time('mysql'),
        'page' => esc_url_raw($redirect),
    ];

    $messages = get_option('coc_contact_messages', []);
    if (!is_array($messages)) { $messages = []; }
    array_unshift($messages, $entry);
    update_option('coc_contact_messages', array_slice($messages, 0, 200), false);

    $labels = [
        'correction' => __('Factual correction', 'compareoxygen'),
        'general' => __('General question', 'compareoxygen'),
        'accessibility' => __('Accessibility issue', 'compareoxygen'),
        'partnership' => __('Partnership enquiry', 'compareoxygen'),
    ];
    $body = sprintf(
        "New message from the contact form\n\nName: %s\nEmail: %s\nTopic: %s\nReceived: %s\n\n%s\n",
        $name,
        $email,
        $labels[$subject],
        $entry['received'],
        $message
    );
    wp_mail(get_option('admin_email'), '[CompareOxygen] ' . $labels[$subject], $body, ['Reply-To: ' . $email]);

    wp_safe_redirect(add_query_arg('contact', 'success', $redirect) . '#contact-form');
    exit;
}
add_action('admin_post_coc_contact', 'compareoxygen_handle_contact');
add_action('admin_post_nopriv_coc_contact', 'compareoxygen_handle_contact');

/** Add useful body classes. */
function compareoxygen_body_classes(array $classes): array {
    if (is_singular()) { $classes[] = 'is-singular'; }
    if (is_front_page()) { $classes[] = 'is-home'; }
    return $classes;
}
add_filter('body_class', 'compareoxygen_body_classes');

/** Excerpt length tuned for cards. */
function compareoxygen_excerpt_length(): int { return 26; }
add_filter('excerpt_length', 'compareoxygen_excerpt_length', 20);

/** Use the model summary for archive cards rather than auto-generated text. */
function compareoxygen_excerpt_more(): string { return '…'; }
add_filter('excerpt_more', 'compareoxygen_excerpt_more');
