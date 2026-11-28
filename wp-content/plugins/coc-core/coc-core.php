<?php
/**
 * Plugin Name: Compare Oxygen Core
 * Description: Evidence-aware product records, approved comparisons, editorial awards, comparison utilities, and structured data.
 * Version: 0.2.0
 * Requires PHP: 8.3
 * Text Domain: coc-core
 */
if (!defined('ABSPATH')) { exit; }

final class COC_Core {
    public const MODEL = 'poc_model';
    public const COMPARISON = 'poc_comparison';
    public const BRAND = 'poc_brand';
    public const USE_CASE = 'poc_use_case';
    public const PAIR_VAR = 'coc_pair';
    private const NONCE = 'coc_save_record';
    private const PAIR_REWRITE_VERSION = '1';

    /** Specification schema: key => [label, group, hint]. */
    public const SPECS = [
        'weight' => ['label' => 'Weight (single battery)', 'group' => 'physical', 'hint' => 'e.g. 4.8 lb (2.2 kg)'],
        'weight_extended' => ['label' => 'Weight (extended battery)', 'group' => 'physical', 'hint' => 'Leave blank when not published.'],
        'dimensions' => ['label' => 'Dimensions (H × W × D)', 'group' => 'physical', 'hint' => 'e.g. 8.1 × 3.3 × 7.2 in'],
        'flow_type' => ['label' => 'Oxygen delivery', 'group' => 'oxygen', 'hint' => 'Pulse dose, continuous flow, or both.'],
        'settings' => ['label' => 'Flow settings', 'group' => 'oxygen', 'hint' => 'e.g. 1–6'],
        'oxygen_output' => ['label' => 'Maximum oxygen output', 'group' => 'oxygen', 'hint' => 'Only when the manufacturer states a figure.'],
        'oxygen_concentration' => ['label' => 'Oxygen concentration', 'group' => 'oxygen', 'hint' => 'Only when the manufacturer states a figure.'],
        'battery_standard' => ['label' => 'Battery life (standard)', 'group' => 'battery', 'hint' => 'e.g. Up to 6.5 hours'],
        'battery_extended' => ['label' => 'Battery life (extended)', 'group' => 'battery', 'hint' => 'e.g. Up to 13 hours'],
        'charge_time' => ['label' => 'Recharge time', 'group' => 'battery', 'hint' => 'Include AC/DC detail when published.'],
        'battery_removable' => ['label' => 'User-replaceable battery', 'group' => 'battery', 'hint' => 'Yes / No — only when stated.'],
        'sound_level' => ['label' => 'Sound level', 'group' => 'noise', 'hint' => 'e.g. 38 dBA at setting 2'],
        'faa_compliant' => ['label' => 'FAA compliant for air travel', 'group' => 'travel', 'hint' => 'Yes / No — cite the manufacturer statement.'],
        'charging_options' => ['label' => 'Charging options', 'group' => 'travel', 'hint' => 'e.g. AC and DC power'],
        'carry_case' => ['label' => 'Carry case', 'group' => 'travel', 'hint' => 'Included / Optional / Not offered'],
        'warranty' => ['label' => 'Manufacturer warranty', 'group' => 'warranty', 'hint' => 'e.g. 3 years (device and sieve beds)'],
        'service_life' => ['label' => 'Expected service life', 'group' => 'warranty', 'hint' => 'Only when the manufacturer publishes it.'],
        'user_replaceable' => ['label' => 'User-replaceable parts', 'group' => 'features', 'hint' => 'e.g. Batteries and sieve beds'],
        'display' => ['label' => 'Display and controls', 'group' => 'features', 'hint' => 'Describe only published features.'],
        'connectivity' => ['label' => 'App / connectivity', 'group' => 'features', 'hint' => 'Only when the manufacturer offers it.'],
        'alarms' => ['label' => 'Alarms and indicators', 'group' => 'features', 'hint' => 'Only when documented.'],
        'price' => ['label' => 'Manufacturer list price', 'group' => 'commercial', 'hint' => 'Leave blank unless an official price is published.'],
    ];

    public const GROUPS = [
        'physical' => 'Size and weight',
        'oxygen' => 'Oxygen delivery',
        'battery' => 'Battery and charging',
        'noise' => 'Noise',
        'travel' => 'Travel',
        'warranty' => 'Warranty and service life',
        'features' => 'Features',
        'commercial' => 'Pricing',
    ];

    public static function boot(): void {
        add_action('init', [self::class, 'register_pair_rewrites'], 9);
        add_action('init', [self::class, 'register_content']);
        add_action('init', [self::class, 'maybe_flush_pair_rewrites'], 99);
        add_action('template_redirect', [self::class, 'handle_pair_redirect'], 1);
        add_action('add_meta_boxes', [self::class, 'register_meta_boxes']);
        add_action('save_post_' . self::MODEL, [self::class, 'save_model'], 10, 2);
        add_action('save_post_' . self::COMPARISON, [self::class, 'save_comparison'], 10, 2);
        add_action('admin_menu', [self::class, 'register_settings_page']);
        add_action('admin_init', [self::class, 'register_settings']);
        add_action('admin_head', [self::class, 'admin_styles']);
        add_filter('manage_' . self::MODEL . '_posts_columns', [self::class, 'model_columns']);
        add_action('manage_' . self::MODEL . '_posts_custom_column', [self::class, 'model_column_content'], 10, 2);
        add_shortcode('coc_compare', [self::class, 'comparison_shortcode']);
        add_shortcode('coc_specs', [self::class, 'specs_shortcode']);
        add_shortcode('coc_award', [self::class, 'award_shortcode']);
        add_action('wp_head', [self::class, 'head_meta'], 5);
        add_action('wp_head', [self::class, 'structured_data'], 20);
        add_filter('use_block_editor_for_post_type', [self::class, 'classic_editor'], 10, 2);
        add_filter('the_content', [self::class, 'append_model_specs'], 20);
        add_filter('get_the_archive_title', [self::class, 'archive_title']);
        add_filter('document_title_parts', [self::class, 'title_parts']);
        add_filter('query_vars', [self::class, 'protect_compare_params']);
        add_filter('query_vars', [self::class, 'register_pair_query_var']);
        add_filter('get_canonical_url', [self::class, 'pair_canonical_url'], 10, 2);
    }

    /**
     * WordPress reserves "second" for time-based queries. The compare tool uses
     * ?first=&second=, so keep "second" out of the main query or /compare/ 404s.
     */
    public static function protect_compare_params(array $vars): array {
        return array_values(array_diff($vars, ['second']));
    }

    public static function register_pair_query_var(array $vars): array {
        $vars[] = self::PAIR_VAR;
        return $vars;
    }

    public static function register_pair_rewrites(): void {
        add_rewrite_rule(
            '^compare/([^/]+)/?$',
            'index.php?pagename=compare&' . self::PAIR_VAR . '=$matches[1]',
            'top'
        );
    }

    /**
     * Flush once after the pair route is introduced. The rewrite is registered
     * on init, so activation alone cannot persist the new rule.
     */
    public static function maybe_flush_pair_rewrites(): void {
        if (get_option('coc_pair_rewrite_version') === self::PAIR_REWRITE_VERSION) {
            return;
        }
        flush_rewrite_rules(false);
        update_option('coc_pair_rewrite_version', self::PAIR_REWRITE_VERSION, false);
    }

    /**
     * Resolve a /compare/{token} token to two published model IDs.
     *
     * The token is matched against complete slug combinations instead of being
     * split at the first "-vs-" or "-and-". A model slug may itself contain a
     * hyphen, so splitting would make valid pairs ambiguous. Legacy "-and-"
     * URLs resolve here and are redirected to the canonical "-vs-" URL.
     */
    public static function resolve_pair_token(string $token): array {
        static $cache = [];
        $token = rawurldecode(trim($token));
        $token = preg_replace('/\.(?:html?|php)$/i', '', $token);
        $token = sanitize_title($token);
        $token = trim((string) preg_replace('/-+/', '-', $token), '-');
        if ($token === '') { return [0, 0]; }
        if (array_key_exists($token, $cache)) { return $cache[$token]; }

        $models = get_posts([
            'post_type' => self::MODEL,
            'post_status' => 'publish',
            'numberposts' => -1,
            'orderby' => 'ID',
            'order' => 'ASC',
        ]);
        $by_slug = [];
        foreach ($models as $model) {
            $slug = (string) $model->post_name;
            if ($slug !== '') { $by_slug[$slug][] = (int) $model->ID; }
        }

        foreach ($models as $first) {
            foreach ($models as $second) {
                if ($first->ID === $second->ID) { continue; }
                $a = (string) $first->post_name;
                $b = (string) $second->post_name;
                if ($a === '' || $b === '') { continue; }
                // Ambiguous duplicate slugs are not safe to resolve by URL.
                if (count($by_slug[$a] ?? []) !== 1 || count($by_slug[$b] ?? []) !== 1) { continue; }
                if ($token === $a . '-vs-' . $b || $token === $a . '-and-' . $b) {
                    return $cache[$token] = [(int) $first->ID, (int) $second->ID];
                }
            }
        }

        return $cache[$token] = [0, 0];
    }

    /**
     * Resolve a /compare/{token} token to two to four published model IDs.
     *
     * Three- and four-model tokens use one "-vs-" between each slug. The parts
     * are matched against complete published slugs, so a hyphen inside a slug
     * is safe; a token with an unknown or duplicated slug falls back to the
     * two-model matcher below.
     */
    public static function resolve_models_token(string $token): array {
        static $cache = [];
        $token = rawurldecode(trim($token));
        $token = preg_replace('/\.(?:html?|php)$/i', '', $token);
        $token = sanitize_title($token);
        $token = trim((string) preg_replace('/-+/', '-', $token), '-');
        if ($token === '') { return []; }
        if (array_key_exists($token, $cache)) { return $cache[$token]; }

        $models = get_posts([
            'post_type' => self::MODEL,
            'post_status' => 'publish',
            'numberposts' => -1,
            'orderby' => 'ID',
            'order' => 'ASC',
        ]);
        $by_slug = [];
        foreach ($models as $model) {
            $slug = (string) $model->post_name;
            if ($slug !== '') { $by_slug[$slug][] = (int) $model->ID; }
        }

        $parts = preg_split('/-vs-/', $token);
        if (is_array($parts) && count($parts) >= 3 && count($parts) <= 4) {
            $ids = [];
            foreach ($parts as $part) {
                $matches = $by_slug[$part] ?? [];
                if (count($matches) !== 1) { $ids = []; break; }
                $ids[] = (int) $matches[0];
            }
            if ($ids && count(array_unique($ids)) === count($parts)) {
                return $cache[$token] = $ids;
            }
        }

        [$a, $b] = self::resolve_pair_token($token);
        return $cache[$token] = ($a && $b) ? [$a, $b] : [];
    }

    /** Keep only published model IDs, de-duplicated, capped at four. */
    public static function sanitize_model_ids(array $ids): array {
        $clean = [];
        foreach ($ids as $id) {
            $id = absint($id);
            if (!$id || in_array($id, $clean, true)) { continue; }
            if (get_post_type($id) !== self::MODEL || get_post_status($id) !== 'publish') { continue; }
            $clean[] = $id;
            if (count($clean) === 4) { break; }
        }
        return $clean;
    }

    public static function requested_model_ids(): array {
        if (!is_page('compare')) { return []; }
        $token = (string) get_query_var(self::PAIR_VAR);
        if ($token !== '') {
            return self::sanitize_model_ids(self::resolve_models_token($token));
        }
        $ids = [];
        foreach (['first', 'second', 'third', 'fourth'] as $key) {
            if (isset($_GET[$key])) { $ids[] = absint(wp_unslash($_GET[$key])); }
        }
        return self::sanitize_model_ids($ids);
    }

    public static function requested_pair_ids(): array {
        $ids = self::requested_model_ids();
        return [$ids[0] ?? 0, $ids[1] ?? 0];
    }

    /**
     * Send query-string pair URLs and legacy/unnormalised path URLs to the one
     * canonical /compare/model-one-vs-model-two/ URL. Invalid pair paths 404.
     */
    public static function handle_pair_redirect(): void {
        if (!is_page('compare')) { return; }

        $token = (string) get_query_var(self::PAIR_VAR);
        $first = isset($_GET['first']) ? absint(wp_unslash($_GET['first'])) : 0;
        $second = isset($_GET['second']) ? absint(wp_unslash($_GET['second'])) : 0;
        $query_ids = self::sanitize_model_ids($first || $second
            ? [$first, $second, absint(wp_unslash($_GET['third'] ?? 0)), absint(wp_unslash($_GET['fourth'] ?? 0))]
            : []);

        if ($token !== '') {
            $ids = self::sanitize_model_ids(self::resolve_models_token($token));
            if (count($ids) < 2) {
                global $wp_query;
                // Stop core's canonical redirect from turning a bad pair into
                // the bare compare page; a missing pair is a genuine 404.
                add_filter('redirect_canonical', '__return_false');
                $wp_query->set_404();
                status_header(404);
                nocache_headers();
                return;
            }

            $canonical = self::compare_url($ids);
            $canonical_path = (string) wp_parse_url($canonical, PHP_URL_PATH);
            $requested_path = (string) wp_parse_url(
                isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '',
                PHP_URL_PATH
            );
            $normalised = untrailingslashit($requested_path) === untrailingslashit($canonical_path);
            $has_pair_query = isset($_GET['first']) || isset($_GET['second']);
            if ($normalised && !$has_pair_query) { return; }

            wp_safe_redirect(self::canonical_redirect_target($canonical), 301, 'Compare Oxygen');
            exit;
        }

        if (count($query_ids) >= 2) {
            wp_safe_redirect(self::canonical_redirect_target(self::compare_url($query_ids)), 301, 'Compare Oxygen');
            exit;
        }
    }

    /** Keep non-pair query arguments (for example campaign parameters) on redirects. */
    private static function canonical_redirect_target(string $canonical): string {
        $args = $_GET;
        unset($args['first'], $args['second'], $args['coc_pair']);
        return $args ? add_query_arg($args, $canonical) : $canonical;
    }

    public static function pair_canonical_url(string $canonical, $post = null): string {
        $ids = self::requested_model_ids();
        return count($ids) >= 2 ? self::compare_url($ids) : $canonical;
    }

    /** Structured fact fields are easier to audit in the classic editor. */
    public static function classic_editor(bool $use, string $post_type): bool {
        return in_array($post_type, [self::MODEL, self::COMPARISON], true) ? false : $use;
    }

    public static function activate(): void {
        self::register_content();
        flush_rewrite_rules();
    }

    public static function deactivate(): void { flush_rewrite_rules(); }

    public static function register_content(): void {
        register_post_type(self::MODEL, [
            'labels' => [
                'name' => __('Oxygen concentrator models', 'coc-core'),
                'singular_name' => __('Model', 'coc-core'),
                'add_new_item' => __('Add model', 'coc-core'),
                'edit_item' => __('Edit model', 'coc-core'),
            ],
            'public' => true,
            'has_archive' => 'reviews',
            'rewrite' => ['slug' => 'reviews', 'with_front' => false],
            'menu_icon' => 'dashicons-products',
            'show_in_rest' => true,
            'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes'],
        ]);
        register_post_type(self::COMPARISON, [
            'labels' => [
                'name' => __('Approved comparisons', 'coc-core'),
                'singular_name' => __('Approved comparison', 'coc-core'),
                'add_new_item' => __('Add comparison', 'coc-core'),
            ],
            'public' => true,
            'has_archive' => 'comparisons',
            'rewrite' => ['slug' => 'comparisons', 'with_front' => false],
            'menu_icon' => 'dashicons-editor-table',
            'show_in_rest' => true,
            'supports' => ['title', 'editor', 'excerpt', 'revisions'],
        ]);
        register_taxonomy(self::BRAND, [self::MODEL], [
            'labels' => ['name' => __('Brands', 'coc-core'), 'singular_name' => __('Brand', 'coc-core')],
            'public' => true,
            'hierarchical' => false,
            'show_in_rest' => true,
            'rewrite' => ['slug' => 'brands', 'with_front' => false],
        ]);
        register_taxonomy(self::USE_CASE, [self::MODEL], [
            'labels' => ['name' => __('Use cases', 'coc-core'), 'singular_name' => __('Use case', 'coc-core')],
            'public' => true,
            'hierarchical' => true,
            'show_in_rest' => true,
            'rewrite' => ['slug' => 'use-case', 'with_front' => false],
        ]);
    }

    public static function register_meta_boxes(): void {
        add_meta_box('coc_model_facts', __('Specifications and evidence', 'coc-core'), [self::class, 'render_model_box'], self::MODEL, 'normal', 'high');
        add_meta_box('coc_model_editorial', __('Editorial summary', 'coc-core'), [self::class, 'render_editorial_box'], self::MODEL, 'side', 'default');
        add_meta_box('coc_approved_pair', __('Approved comparison pair', 'coc-core'), [self::class, 'render_comparison_box'], self::COMPARISON, 'side', 'high');
    }

    public static function admin_styles(): void {
        echo '<style>
        .coc-spec-table{width:100%;border-collapse:collapse;margin:8px 0 20px}
        .coc-spec-table th,.coc-spec-table td{border-bottom:1px solid #e2e6ea;padding:8px 6px;text-align:left;vertical-align:top}
        .coc-spec-table th{font-size:13px;text-transform:uppercase;letter-spacing:.05em;color:#50575e}
        .coc-spec-table input[type=text],.coc-spec-table input[type=url],.coc-spec-table input[type=date]{width:100%}
        .coc-group-title{margin:22px 0 0;padding-top:14px;border-top:1px solid #dcdcde;font-size:16px}
        .coc-help{max-width:760px;color:#50575e}
        </style>';
    }

    private static function field(string $name, string $label, string $value, string $type = 'text', string $placeholder = ''): void {
        printf(
            '<p><label for="%1$s"><strong>%2$s</strong></label><br><input class="widefat" type="%3$s" id="%1$s" name="%1$s" value="%4$s" placeholder="%5$s"></p>',
            esc_attr($name), esc_html($label), esc_attr($type), esc_attr($value), esc_attr($placeholder)
        );
    }

    public static function render_model_box(WP_Post $post): void {
        wp_nonce_field(self::NONCE, self::NONCE);
        echo '<p class="coc-help">' . esc_html__('Enter only facts published by the manufacturer. Every fact needs its own source URL and verification date; incomplete facts display publicly as “Not confirmed”. Never estimate, convert, or infer a missing value.', 'coc-core') . '</p>';
        $current_group = '';
        foreach (self::SPECS as $key => $spec) {
            if ($spec['group'] !== $current_group) {
                if ($current_group !== '') { echo '</tbody></table>'; }
                $current_group = $spec['group'];
                echo '<h3 class="coc-group-title">' . esc_html(self::GROUPS[$current_group] ?? $current_group) . '</h3>';
                echo '<table class="coc-spec-table"><thead><tr><th style="width:24%">' . esc_html__('Specification', 'coc-core') . '</th><th style="width:26%">' . esc_html__('Published value', 'coc-core') . '</th><th style="width:32%">' . esc_html__('Source URL', 'coc-core') . '</th><th style="width:18%">' . esc_html__('Verified on', 'coc-core') . '</th></tr></thead><tbody>';
            }
            $value = (string) get_post_meta($post->ID, '_coc_' . $key, true);
            $source = (string) get_post_meta($post->ID, '_coc_' . $key . '_source', true);
            $verified = (string) get_post_meta($post->ID, '_coc_' . $key . '_verified', true);
            printf(
                '<tr><th scope="row"><label for="coc_%1$s">%2$s</label><br><small>%3$s</small></th><td><input type="text" id="coc_%1$s" name="coc_%1$s" value="%4$s" placeholder="%5$s"></td><td><input type="url" id="coc_%1$s_source" name="coc_%1$s_source" value="%6$s" placeholder="https://"></td><td><input type="date" id="coc_%1$s_verified" name="coc_%1$s_verified" value="%7$s"></td></tr>',
                esc_attr($key), esc_html($spec['label']), esc_html($spec['hint']), esc_attr($value), esc_attr($spec['hint']), esc_attr($source), esc_attr($verified)
            );
        }
        echo '</tbody></table>';
        self::field('coc_model_number', __('Manufacturer model number', 'coc-core'), (string) get_post_meta($post->ID, '_coc_model_number', true));
        self::field('coc_best_for', __('Best suited to (editorial)', 'coc-core'), (string) get_post_meta($post->ID, '_coc_best_for', true));
    }

    public static function render_editorial_box(WP_Post $post): void {
        echo '<p>' . esc_html__('Use the main editor for the review, the excerpt for the card summary, and the featured image only when you hold documented image rights.', 'coc-core') . '</p>';
        self::field('coc_pros', __('Strengths (one per line)', 'coc-core'), (string) get_post_meta($post->ID, '_coc_pros', true));
        echo '<p><label for="coc_pros"><small>' . esc_html__('Stored as plain text, one item per line.', 'coc-core') . '</small></label></p>';
        self::field('coc_cons', __('Limitations (one per line)', 'coc-core'), (string) get_post_meta($post->ID, '_coc_cons', true));
        self::field('coc_evidence_level', __('Evidence level', 'coc-core'), (string) get_post_meta($post->ID, '_coc_evidence_level', true), 'text', 'Manufacturer specifications reviewed');
    }

    public static function render_comparison_box(WP_Post $post): void {
        wp_nonce_field(self::NONCE, self::NONCE);
        $models = get_posts(['post_type' => self::MODEL, 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC']);
        foreach (['first' => __('First model', 'coc-core'), 'second' => __('Second model', 'coc-core')] as $key => $label) {
            $selected = absint(get_post_meta($post->ID, '_coc_' . $key . '_model', true));
            echo '<p><label for="coc_' . esc_attr($key) . '"><strong>' . esc_html($label) . '</strong></label><select class="widefat" id="coc_' . esc_attr($key) . '" name="coc_' . esc_attr($key) . '"><option value="">' . esc_html__('Select a published model', 'coc-core') . '</option>';
            foreach ($models as $model) {
                printf('<option value="%1$d" %2$s>%3$s</option>', absint($model->ID), selected($selected, $model->ID, false), esc_html($model->post_title));
            }
            echo '</select></p>';
        }
        echo '<p class="coc-help">' . esc_html__('Publish a comparison only after both model records are reviewed. The pair is indexed at its own URL so that generated permutations stay out of search.', 'coc-core') . '</p>';
    }

    private static function can_save(int $post_id, WP_Post $post, string $post_type): bool {
        if (!isset($_POST[self::NONCE]) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[self::NONCE])), self::NONCE)) { return false; }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return false; }
        if (wp_is_post_revision($post_id)) { return false; }
        return current_user_can('edit_post', $post_id) && $post->post_type === $post_type;
    }

    public static function save_model(int $post_id, WP_Post $post): void {
        if (!self::can_save($post_id, $post, self::MODEL)) { return; }
        foreach (array_keys(self::SPECS) as $key) {
            $name = 'coc_' . $key;
            $value = isset($_POST[$name]) ? sanitize_text_field(wp_unslash($_POST[$name])) : '';
            $source = isset($_POST[$name . '_source']) ? esc_url_raw(wp_unslash($_POST[$name . '_source'])) : '';
            $date = isset($_POST[$name . '_verified']) ? sanitize_text_field(wp_unslash($_POST[$name . '_verified'])) : '';
            if ($value === '') {
                update_post_meta($post_id, '_coc_' . $key, '');
                update_post_meta($post_id, '_coc_' . $key . '_source', '');
                update_post_meta($post_id, '_coc_' . $key . '_verified', '');
                continue;
            }
            update_post_meta($post_id, '_coc_' . $key, $value);
            update_post_meta($post_id, '_coc_' . $key . '_source', $source);
            update_post_meta($post_id, '_coc_' . $key . '_verified', preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) ? $date : '');
        }
        foreach (['model_number', 'best_for', 'pros', 'cons', 'evidence_level'] as $key) {
            $name = 'coc_' . $key;
            $raw = isset($_POST[$name]) ? wp_unslash($_POST[$name]) : '';
            update_post_meta($post_id, '_coc_' . $key, sanitize_textarea_field($raw));
        }
        if (function_exists('coc_flush_compare_cache')) { coc_flush_compare_cache(); }
    }

    public static function save_comparison(int $post_id, WP_Post $post): void {
        if (!self::can_save($post_id, $post, self::COMPARISON)) { return; }
        foreach (['first', 'second'] as $key) {
            $model_id = isset($_POST['coc_' . $key]) ? absint($_POST['coc_' . $key]) : 0;
            if ($model_id && get_post_type($model_id) !== self::MODEL) { $model_id = 0; }
            update_post_meta($post_id, '_coc_' . $key . '_model', $model_id);
        }
    }

    public static function register_settings_page(): void {
        add_submenu_page(
            'edit.php?post_type=' . self::MODEL,
            __('Editorial awards', 'coc-core'),
            __('Editorial awards', 'coc-core'),
            'manage_options',
            'coc-awards',
            [self::class, 'render_settings_page']
        );
    }

    public static function register_settings(): void {
        register_setting('coc_awards', 'coc_awards', [
            'type' => 'array',
            'sanitize_callback' => [self::class, 'sanitize_awards'],
            'default' => [],
        ]);
    }

    public static function sanitize_awards($input): array {
        $clean = [];
        if (!is_array($input)) { return $clean; }
        foreach ($input as $row) {
            if (!is_array($row)) { continue; }
            $label = isset($row['label']) ? sanitize_text_field($row['label']) : '';
            $model_id = isset($row['model']) ? absint($row['model']) : 0;
            if ($label === '' || !$model_id || get_post_type($model_id) !== self::MODEL) { continue; }
            $clean[] = [
                'label' => $label,
                'model' => $model_id,
                'why' => isset($row['why']) ? sanitize_textarea_field($row['why']) : '',
            ];
        }
        return $clean;
    }

    public static function render_settings_page(): void {
        if (!current_user_can('manage_options')) { return; }
        $awards = (array) get_option('coc_awards', []);
        $models = get_posts(['post_type' => self::MODEL, 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC']);
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Editorial awards', 'coc-core'); ?></h1>
            <p class="coc-help"><?php esc_html_e('Awards are editorial decisions, not automatic rankings. Each award appears on the homepage and the Best Of guide with the rationale you write here. Leave a slot empty to hide it.', 'coc-core'); ?></p>
            <form method="post" action="options.php">
                <?php settings_fields('coc_awards'); ?>
                <table class="widefat striped">
                    <thead><tr><th style="width:18%"><?php esc_html_e('Award label', 'coc-core'); ?></th><th style="width:24%"><?php esc_html_e('Model', 'coc-core'); ?></th><th><?php esc_html_e('Why it was selected', 'coc-core'); ?></th></tr></thead>
                    <tbody>
                    <?php $rows = array_pad(array_slice($awards, 0, 6), 6, ['label' => '', 'model' => 0, 'why' => '']); ?>
                    <?php foreach ($rows as $index => $row) : ?>
                        <tr>
                            <td><input type="text" class="widefat" name="coc_awards[<?php echo (int) $index; ?>][label]" value="<?php echo esc_attr($row['label'] ?? ''); ?>" placeholder="<?php esc_attr_e('Best Overall', 'coc-core'); ?>"></td>
                            <td>
                                <select class="widefat" name="coc_awards[<?php echo (int) $index; ?>][model]">
                                    <option value="0"><?php esc_html_e('— None —', 'coc-core'); ?></option>
                                    <?php foreach ($models as $model) : ?>
                                        <option value="<?php echo absint($model->ID); ?>" <?php selected((int) ($row['model'] ?? 0), $model->ID); ?>><?php echo esc_html($model->post_title); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><textarea class="widefat" rows="2" name="coc_awards[<?php echo (int) $index; ?>][why]" placeholder="<?php esc_attr_e('Two sentences explaining the editorial rationale, based on verified facts only.', 'coc-core'); ?>"><?php echo esc_textarea($row['why'] ?? ''); ?></textarea></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public static function model_columns(array $columns): array {
        $new = [];
        foreach ($columns as $key => $label) {
            $new[$key] = $label;
            if ($key === 'title') {
                $new['coc_brand'] = __('Brand', 'coc-core');
                $new['coc_weight'] = __('Weight', 'coc-core');
                $new['coc_verified'] = __('Verified facts', 'coc-core');
            }
        }
        return $new;
    }

    public static function model_column_content(string $column, int $post_id): void {
        if ($column === 'coc_brand') {
            $terms = get_the_terms($post_id, self::BRAND);
            echo is_array($terms) ? esc_html(implode(', ', wp_list_pluck($terms, 'name'))) : '—';
        } elseif ($column === 'coc_weight') {
            echo esc_html(self::spec($post_id, 'weight')['display']);
        } elseif ($column === 'coc_verified') {
            echo esc_html(sprintf('%d of %d', self::verified_count($post_id), count(self::SPECS)));
        }
    }

    /* ---------------------------------------------------------------
     * Public data helpers (used by theme templates and shortcodes).
     * ------------------------------------------------------------- */

    public static function models(array $args = []): array {
        return get_posts(wp_parse_args($args, [
            'post_type' => self::MODEL,
            'post_status' => 'publish',
            'numberposts' => -1,
            'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
        ]));
    }

    public static function model_by_slug(string $slug): ?WP_Post {
        $post = get_page_by_path($slug, OBJECT, self::MODEL);
        return $post instanceof WP_Post ? $post : null;
    }

    public static function brand_of(int $model_id): string {
        $terms = get_the_terms($model_id, self::BRAND);
        if (is_array($terms) && $terms) { return $terms[0]->name; }
        return '';
    }

    public static function brand_link(int $model_id): string {
        $terms = get_the_terms($model_id, self::BRAND);
        if (is_array($terms) && $terms) {
            $link = get_term_link($terms[0]);
            return is_wp_error($link) ? '' : $link;
        }
        return '';
    }

    /**
     * One specification with evidence state.
     * @return array{value:string,source:string,verified:string,complete:bool,display:string}
     */
    public static function spec(int $model_id, string $key): array {
        $value = (string) get_post_meta($model_id, '_coc_' . $key, true);
        $source = (string) get_post_meta($model_id, '_coc_' . $key . '_source', true);
        $verified = (string) get_post_meta($model_id, '_coc_' . $key . '_verified', true);
        $complete = $value !== '' && $source !== '' && $verified !== '' && (bool) strtotime($verified);
        return [
            'value' => $value,
            'source' => $source,
            'verified' => $verified,
            'complete' => $complete,
            'display' => $complete ? $value : '',
        ];
    }

    public static function verified_count(int $model_id): int {
        $count = 0;
        foreach (array_keys(self::SPECS) as $key) {
            if (self::spec($model_id, $key)['complete']) { $count++; }
        }
        return $count;
    }

    public static function completeness(int $model_id): int {
        return (int) round(self::verified_count($model_id) / max(1, count(self::SPECS)) * 100);
    }

    /** Latest verified-on date across all specs. */
    public static function last_verified(int $model_id): string {
        $latest = '';
        foreach (array_keys(self::SPECS) as $key) {
            $date = self::spec($model_id, $key)['verified'];
            if ($date !== '' && $date > $latest) { $latest = $date; }
        }
        return $latest;
    }

    public static function min_verified_between(int $first, int $second): string {
        return self::min_verified_for([$first, $second]);
    }

    public static function min_verified_for(array $ids): string {
        $dates = [];
        foreach ($ids as $id) {
            $date = self::last_verified((int) $id);
            if ($date !== '') { $dates[] = $date; }
        }
        if (!$dates) { return ''; }
        sort($dates);
        return $dates[0];
    }

    /** Approved comparison post for a model pair, if one exists. */
    public static function comparison_for_pair(int $first, int $second): ?WP_Post {
        $query = new WP_Query([
            'post_type' => self::COMPARISON,
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'no_found_rows' => true,
            'meta_query' => [
                'relation' => 'OR',
                [
                    'relation' => 'AND',
                    ['key' => '_coc_first_model', 'value' => $first, 'compare' => '=', 'type' => 'NUMERIC'],
                    ['key' => '_coc_second_model', 'value' => $second, 'compare' => '=', 'type' => 'NUMERIC'],
                ],
                [
                    'relation' => 'AND',
                    ['key' => '_coc_first_model', 'value' => $second, 'compare' => '=', 'type' => 'NUMERIC'],
                    ['key' => '_coc_second_model', 'value' => $first, 'compare' => '=', 'type' => 'NUMERIC'],
                ],
            ],
        ]);
        return $query->posts ? $query->posts[0] : null;
    }

    public static function comparison_ids(int $comparison_id): array {
        return [
            absint(get_post_meta($comparison_id, '_coc_first_model', true)),
            absint(get_post_meta($comparison_id, '_coc_second_model', true)),
        ];
    }

    /** Resolve a pair to the canonical comparison-tool URL. */
    public static function pair_url(int $first, int $second): string {
        return self::compare_url([$first, $second]);
    }

    /** Canonical /compare/model-one-vs-model-two[-vs-model-three]/ URL. */
    public static function compare_url(array $ids): string {
        $ids = self::sanitize_model_ids($ids);
        $slugs = [];
        foreach ($ids as $id) {
            $post = get_post($id);
            if (!$post instanceof WP_Post || $post->post_name === '') {
                return self::compare_tool_url();
            }
            $slugs[] = $post->post_name;
        }
        if (count($slugs) < 2 || count(array_unique($slugs)) !== count($slugs)) {
            return self::compare_tool_url();
        }
        return home_url('/compare/' . implode('-vs-', $slugs) . '/');
    }

    public static function compare_tool_url(): string {
        $page = get_page_by_path('compare');
        return $page ? get_permalink($page) : home_url('/compare/');
    }

    /** Comparison data grouped for rendering. */
    public static function comparison_groups(int $first, int $second): array {
        return self::comparison_groups_for([$first, $second]);
    }

    /**
     * Grouped specification rows for two to four models. Each row carries a
     * `specs` list in model order plus `first`/`second` aliases so existing
     * two-model templates keep working unchanged.
     */
    public static function comparison_groups_for(array $ids): array {
        // Duplicate IDs are meaningful here: a model page asks for ($id, $id)
        // to read its own rows, so keep the list as given.
        $ids = array_slice(array_values(array_filter(array_map('absint', $ids))), 0, 4);
        if (count($ids) < 2) { return []; }
        $groups = [];
        foreach (self::SPECS as $key => $spec) {
            $values = [];
            $confirmed = 0;
            foreach ($ids as $id) {
                $value = self::spec($id, $key);
                $values[] = $value;
                if ($value['complete']) { $confirmed++; }
            }
            if (!$confirmed) { continue; }

            $same = true;
            $reference = null;
            foreach ($values as $value) {
                if (!$value['complete']) { $same = false; break; }
                $needle = strtolower(trim($value['display']));
                if ($reference === null) { $reference = $needle; }
                elseif ($needle !== $reference) { $same = false; break; }
            }

            $row = ['label' => $spec['label'], 'specs' => $values, 'same' => $same];
            if (count($values) === 2) {
                $row['first'] = $values[0];
                $row['second'] = $values[1];
            }
            $groups[$spec['group']][$key] = $row;
        }
        return array_filter($groups);
    }

    public static function difference_count(int $first, int $second): int {
        return self::difference_count_for([$first, $second]);
    }

    public static function difference_count_for(array $ids): int {
        $count = 0;
        foreach (self::comparison_groups_for($ids) as $rows) {
            foreach ($rows as $row) {
                if (!$row['same']) { $count++; }
            }
        }
        return $count;
    }

    public static function awards(): array {
        $awards = (array) get_option('coc_awards', []);
        $resolved = [];
        foreach ($awards as $award) {
            $model = get_post((int) ($award['model'] ?? 0));
            if (!$model instanceof WP_Post || $model->post_status !== 'publish' || $model->post_type !== self::MODEL) { continue; }
            $award['post'] = $model;
            $resolved[] = $award;
        }
        return $resolved;
    }

    public static function award_for_model(int $model_id): string {
        foreach (self::awards() as $award) {
            if ((int) $award['model'] === $model_id) { return (string) $award['label']; }
        }
        return '';
    }

    public static function pros(int $model_id): array {
        return self::lines((string) get_post_meta($model_id, '_coc_pros', true));
    }

    public static function cons(int $model_id): array {
        return self::lines((string) get_post_meta($model_id, '_coc_cons', true));
    }

    private static function lines(string $text): array {
        $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];
        return array_values(array_filter(array_map('trim', $lines), static fn($line) => $line !== ''));
    }

    /* ---------------------------------------------------------------
     * Shortcodes.
     * ------------------------------------------------------------- */

    public static function model_options(array $models, int $selected = 0, int $exclude = 0): string {
        $by_brand = [];
        foreach ($models as $model) {
            if ((int) $model->ID === $exclude) { continue; }
            $brand = self::brand_of($model->ID) ?: __('Other', 'coc-core');
            $by_brand[$brand][] = $model;
        }
        ksort($by_brand);
        $html = '<option value="">' . esc_html__('Select a concentrator', 'coc-core') . '</option>';
        foreach ($by_brand as $brand => $items) {
            $html .= '<optgroup label="' . esc_attr($brand) . '">';
            foreach ($items as $model) {
                $html .= sprintf(
                    '<option value="%1$d" data-brand="%2$s" data-weight="%3$s" %4$s>%5$s</option>',
                    absint($model->ID),
                    esc_attr($brand),
                    esc_attr(self::spec($model->ID, 'weight')['display']),
                    selected($selected, $model->ID, false),
                    esc_html($model->post_title)
                );
            }
            $html .= '</optgroup>';
        }
        return $html;
    }

    public static function comparison_shortcode(array $atts = []): string {
        $models = self::models();
        if (count($models) < 2) {
            return '<p class="coc-empty">' . esc_html__('Comparison is available once at least two models have published, source-checked specifications.', 'coc-core') . '</p>';
        }
        $first = isset($_GET['first']) ? absint(wp_unslash($_GET['first'])) : 0;
        $second = isset($_GET['second']) ? absint(wp_unslash($_GET['second'])) : 0;
        $args = shortcode_atts(['compact' => 'false'], $atts, 'coc_compare');
        $is_compact = $args['compact'] === 'true';
        ob_start();
        get_template_part('template-parts/compare', 'widget', ['models' => $models, 'first' => $first, 'second' => $second, 'compact' => $is_compact]);
        if ($first && $second && $first !== $second && get_post_status($first) === 'publish' && get_post_status($second) === 'publish' && get_post_type($first) === self::MODEL && get_post_type($second) === self::MODEL) {
            get_template_part('template-parts/compare', 'results', ['first' => $first, 'second' => $second]);
        }
        return (string) ob_get_clean();
    }

    public static function specs_shortcode(array $atts = []): string {
        $atts = shortcode_atts(['id' => get_the_ID()], $atts, 'coc_specs');
        $id = absint($atts['id']);
        if (get_post_type($id) !== self::MODEL) { return ''; }
        ob_start();
        get_template_part('template-parts/spec', 'table', ['model_id' => $id]);
        return (string) ob_get_clean();
    }

    public static function award_shortcode(array $atts = []): string {
        $atts = shortcode_atts(['label' => '', 'model' => ''], $atts, 'coc_award');
        $model = self::model_by_slug(sanitize_title($atts['model']));
        if (!$model) { return ''; }
        ob_start();
        get_template_part('template-parts/award', 'card', ['label' => $atts['label'], 'model' => $model]);
        return (string) ob_get_clean();
    }

    /** Add verified specs to model content when the editor has not placed them. */
    public static function append_model_specs(string $content): string {
        if (!is_singular(self::MODEL) || !in_the_loop() || !is_main_query()) { return $content; }
        if (has_shortcode($content, 'coc_specs')) { return $content; }
        return $content;
    }

    /* ---------------------------------------------------------------
     * SEO output.
     * ------------------------------------------------------------- */

    public static function head_meta(): void {
        $description = '';
        $compare_ids = self::requested_model_ids();
        if (count($compare_ids) >= 2) {
            $names = array_map(static function ($id) { return 'the ' . get_the_title($id); }, $compare_ids);
            $last = array_pop($names);
            $list = $names ? implode(', ', $names) . ' and ' . $last : $last;
            $description = sprintf(
                'Compare the published specifications of %s: weight, battery life, oxygen delivery, noise, travel, warranty and features, with a source and verification date for every confirmed fact.',
                $list
            );
        } elseif (is_front_page()) {
            $description = get_bloginfo('description') ?: 'Independent portable oxygen concentrator research: manufacturer-sourced specifications, side-by-side comparisons, and plain-English buying guides.';
        } elseif (is_singular()) {
            $post = get_queried_object();
            if ($post instanceof WP_Post) {
                $description = $post->post_excerpt ?: wp_trim_words(wp_strip_all_tags($post->post_content), 32, '…');
                if ($post->post_type === self::MODEL) {
                    $description = sprintf(
                        '%s review: verified weight, battery life, flow settings, noise and travel specifications, with sources and verification dates.',
                        $post->post_title
                    );
                }
            }
        } elseif (is_tax(self::BRAND)) {
            $term = get_queried_object();
            $description = $term instanceof WP_Term ? ($term->description ?: sprintf('Portable oxygen concentrator models from %s, with sourced specifications and reviews.', $term->name)) : '';
        } elseif (is_post_type_archive()) {
            $description = 'Browse every portable oxygen concentrator review and comparison on CompareOxygenConcentrators.com.';
        }
        if ($description) {
            $description = wp_trim_words($description, 40, '…');
            printf('<meta name="description" content="%s">' . "\n", esc_attr($description));
            printf('<meta property="og:description" content="%s">' . "\n", esc_attr($description));
            printf('<meta name="twitter:description" content="%s">' . "\n", esc_attr($description));
        }
        $title = wp_get_document_title();
        printf('<meta property="og:title" content="%s">' . "\n", esc_attr($title));
        printf('<meta property="og:type" content="%s">' . "\n", is_singular() && !is_front_page() ? 'article' : 'website');
        $og_url = is_singular() ? get_permalink() : home_url(add_query_arg([]));
        $og_url = apply_filters('get_canonical_url', $og_url, get_queried_object());
        printf('<meta property="og:url" content="%s">' . "\n", esc_url($og_url));
        printf('<meta property="og:site_name" content="%s">' . "\n", esc_attr(get_bloginfo('name')));
        $og_image = get_theme_file_uri('assets/img/og-default.png');
        if ($og_image) {
            printf('<meta property="og:image" content="%s">' . "\n", esc_url($og_image));
            printf('<meta property="og:image:width" content="1200">' . "\n");
            printf('<meta property="og:image:height" content="630">' . "\n");
        }
        printf('<meta name="twitter:card" content="summary_large_image">' . "\n");
        if ($og_image) {
            printf('<meta name="twitter:image" content="%s">' . "\n", esc_url($og_image));
        }
        if (is_singular()) {
            $modified = get_post_modified_time('c', true);
            if ($modified) { printf('<meta property="article:modified_time" content="%s">' . "\n", esc_attr($modified)); }
        }
        if (self::is_dynamic_compare() && !get_query_var(self::PAIR_VAR)) {
            echo '<meta name="robots" content="noindex,follow">' . "\n";
        }
        if (is_search() || is_404()) {
            echo '<meta name="robots" content="noindex,follow">' . "\n";
        }
        if (!is_singular() && !is_search() && !is_404()) {
            $path = wp_parse_url(isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '/', PHP_URL_PATH);
            printf('<link rel="canonical" href="%s">' . "\n", esc_url(home_url($path ?: '/')));
        }
    }

    public static function is_dynamic_compare(): bool {
        $page = get_page_by_path('compare');
        if (!$page) { return false; }
        $is_compare_page = is_page($page->ID);
        return $is_compare_page && (
            get_query_var(self::PAIR_VAR)
            || isset($_GET['first'])
            || isset($_GET['second'])
        );
    }

    public static function structured_data(): void {
        $graph = [];
        $home = home_url('/');
        if (is_front_page()) {
            $graph[] = [
                '@type' => 'WebSite',
                '@id' => $home . '#website',
                'url' => $home,
                'name' => get_bloginfo('name'),
                'description' => get_bloginfo('description'),
                'publisher' => ['@id' => $home . '#organization'],
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => ['@type' => 'EntryPoint', 'urlTemplate' => $home . '?s={search_term_string}'],
                    'query-input' => 'required name=search_term_string',
                ],
            ];
            $graph[] = [
                '@type' => 'Organization',
                '@id' => $home . '#organization',
                'name' => 'CompareOxygenConcentrators.com',
                'url' => $home,
                'description' => 'Independent research and comparison platform for portable oxygen concentrators sold in the United States.',
            ];
        }

        if (is_singular(self::MODEL)) {
            $id = get_queried_object_id();
            $specs = [];
            foreach (self::comparison_groups($id, $id) as $rows) {
                foreach ($rows as $row) {
                    $specs[] = ['@type' => 'PropertyValue', 'name' => $row['label'], 'value' => $row['first']['display']];
                }
            }
            $product = [
                '@type' => 'Product',
                'name' => get_the_title($id),
                'description' => wp_strip_all_tags(get_the_excerpt($id)),
                'url' => get_permalink($id),
                'brand' => ['@type' => 'Brand', 'name' => self::brand_of($id)],
                'category' => 'Portable oxygen concentrator',
            ];
            if ($specs) { $product['additionalProperty'] = $specs; }
            $graph[] = $product;
        }

        if (is_singular() && !is_front_page()) {
            $post = get_queried_object();
            if ($post instanceof WP_Post && in_array($post->post_type, ['post', self::MODEL, self::COMPARISON], true)) {
                $graph[] = [
                    '@type' => 'Article',
                    'headline' => get_the_title($post),
                    'description' => wp_strip_all_tags(get_the_excerpt($post)),
                    'datePublished' => get_post_time('c', true, $post),
                    'dateModified' => get_post_modified_time('c', true, $post),
                    'mainEntityOfPage' => get_permalink($post),
                    'author' => ['@type' => 'Organization', 'name' => 'CompareOxygenConcentrators.com Editorial Team'],
                    'publisher' => ['@id' => $home . '#organization'],
                ];
            }
        }

        if ($graph) {
            echo '<script type="application/ld+json">' . wp_json_encode(['@context' => 'https://schema.org', '@graph' => $graph], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
        }
    }

    public static function archive_title(string $title): string {
        if (is_post_type_archive(self::MODEL)) { return __('Portable oxygen concentrator reviews', 'coc-core'); }
        if (is_post_type_archive(self::COMPARISON)) { return __('Portable oxygen concentrator comparisons', 'coc-core'); }
        return $title;
    }

    public static function title_parts(array $parts): array {
        $ids = self::requested_model_ids();
        if (count($ids) >= 2) {
            $parts['title'] = implode(' vs ', array_map('get_the_title', $ids)) . ' comparison';
            unset($parts['tagline']);
            return $parts;
        }
        if (is_front_page()) {
            $parts['title'] = __('Portable Oxygen Concentrator Reviews & Comparisons', 'coc-core');
            unset($parts['tagline']);
        }
        if (is_singular(self::MODEL)) {
            $parts['title'] = sprintf('%s review and specifications', get_the_title());
        }
        return $parts;
    }
}

COC_Core::boot();
register_activation_hook(__FILE__, [COC_Core::class, 'activate']);
register_deactivation_hook(__FILE__, [COC_Core::class, 'deactivate']);

/* Convenience wrappers for theme templates. */
function coc_models(array $args = []): array { return COC_Core::models($args); }
function coc_spec(int $id, string $key): array { return COC_Core::spec($id, $key); }
function coc_verified_count(int $id): int { return COC_Core::verified_count($id); }
function coc_completeness(int $id): int { return COC_Core::completeness($id); }
function coc_brand_of(int $id): string { return COC_Core::brand_of($id); }
function coc_brand_link(int $id): string { return COC_Core::brand_link($id); }
function coc_award_label(int $id): string { return COC_Core::award_for_model($id); }
function coc_awards(): array { return COC_Core::awards(); }
function coc_pair_url(int $a, int $b): string { return COC_Core::pair_url($a, $b); }
function coc_pair_ids(): array { return COC_Core::requested_pair_ids(); }
function coc_compare_ids(): array { return COC_Core::requested_model_ids(); }
function coc_compare_url(array $ids): string { return COC_Core::compare_url($ids); }
function coc_compare_groups(array $ids): array { return COC_Core::comparison_groups_for($ids); }
function coc_compare_difference_count(array $ids): int { return COC_Core::difference_count_for($ids); }
function coc_compare_min_verified(array $ids): string { return COC_Core::min_verified_for($ids); }
function coc_comparison_for_pair(int $a, int $b): ?WP_Post { return COC_Core::comparison_for_pair($a, $b); }
function coc_compare_tool_url(): string { return COC_Core::compare_tool_url(); }
function coc_pros(int $id): array { return COC_Core::pros($id); }
function coc_cons(int $id): array { return COC_Core::cons($id); }
function coc_last_verified(int $id): string { return COC_Core::last_verified($id); }
