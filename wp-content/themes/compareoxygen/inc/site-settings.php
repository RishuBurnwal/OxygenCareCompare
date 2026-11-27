<?php
/**
 * Admin-editable site copy and widget areas.
 *
 * @package compareoxygen
 */
if (!defined('ABSPATH')) { exit; }

/**
 * Default copy for every editable field. Defaults mirror the strings the
 * templates used before this screen existed, so a fresh install renders the
 * same page until an editor changes something.
 */
function coc_default_settings(): array {
    return [
        'utility_text'      => __('Independent research · every specification carries a source and verification date', 'compareoxygen'),
        'header_cta_label'  => __('Compare', 'compareoxygen'),

        'footer_mission'    => __('Plain-English research for people choosing a portable oxygen concentrator. Manufacturer-stated specifications, visible sources, and comparisons that admit what is not yet confirmed.', 'compareoxygen'),
        'footer_badge_1'    => __('Source-checked facts', 'compareoxygen'),
        'footer_badge_2'    => __('No paid rankings', 'compareoxygen'),
        'footer_disclaimer' => __('Medical information limitation: CompareOxygenConcentrators.com publishes general educational information and product research. It is not medical advice and does not establish whether a portable oxygen concentrator is clinically appropriate for any individual. Oxygen prescriptions, flow settings, and travel requirements must be confirmed with a qualified clinician and your oxygen supplier. Specifications change without notice — always confirm details with the manufacturer before purchase.', 'compareoxygen'),
        'footer_copyright'  => __('All rights reserved.', 'compareoxygen'),

        'hero_eyebrow'      => __('Independent concentrator research', 'compareoxygen'),
        'hero_title'        => __('Compare portable oxygen concentrators', 'compareoxygen'),
        'hero_lede'         => __('Make sense of weight, battery life, flow settings, and travel rules before you choose. Every published specification carries a manufacturer source and the date we verified it — and gaps stay visible.', 'compareoxygen'),
        'hero_cta1_label'   => __('Compare concentrators', 'compareoxygen'),
        'hero_cta1_url'     => '',
        'hero_cta2_label'   => __('See our top picks', 'compareoxygen'),
        'hero_cta2_url'     => '#top-picks',
        'hero_meta_1'       => __('Manufacturer sources shown on every fact', 'compareoxygen'),
        'hero_meta_2'       => __('No paid rankings or invented scores', 'compareoxygen'),

        'trust_1'           => __('Sources attached to every published specification', 'compareoxygen'),
        'trust_2'           => __('Verification dates recorded', 'compareoxygen'),
        'trust_3'           => __('Editorial awards, never automatic', 'compareoxygen'),
        'trust_4'           => __('Travel claims tied to manufacturer statements', 'compareoxygen'),

        'picks_eyebrow'     => __('Top picks', 'compareoxygen'),
        'picks_title'       => __('Portable oxygen concentrators we have documented', 'compareoxygen'),
        'picks_intro'       => __('A starting point, not a prescription. Each card shows only verified specifications and links to the full evidence record.', 'compareoxygen'),

        'cases_eyebrow'     => __('Start with your situation', 'compareoxygen'),
        'cases_title'       => __('Popular comparison categories', 'compareoxygen'),
        'cases_intro'       => __('Different routines stress different parts of a concentrator. Choose the context that sounds most like your week.', 'compareoxygen'),

        'awards_eyebrow'    => __('Editorial awards', 'compareoxygen'),
        'awards_title'      => __('Best overall, best lightweight, best for travel', 'compareoxygen'),
        'awards_intro'      => __('Awards are assigned by our editors after reviewing the verified record. They are never generated automatically from a formula or a commission rate.', 'compareoxygen'),

        'steps_eyebrow'     => __('How comparison works', 'compareoxygen'),
        'steps_title'       => __('Three steps from a shortlist to a clear answer', 'compareoxygen'),
        'steps_intro'       => __('The comparison tool is deliberately plain: choose, read, and verify. No hidden ranking, no fake precision.', 'compareoxygen'),
        'step1_title'       => __('Choose two models', 'compareoxygen'),
        'step1_body'        => __('Search by brand or model. Both sides are treated equally; the tool will not let you compare a product with itself.', 'compareoxygen'),
        'step2_title'       => __('Read the differences', 'compareoxygen'),
        'step2_body'        => __('Specifications are grouped the way buyers think: size, oxygen delivery, battery, noise, travel, warranty, and features.', 'compareoxygen'),
        'step3_title'       => __('Check the evidence', 'compareoxygen'),
        'step3_body'        => __('Every confirmed cell links to the manufacturer source and shows when it was verified. Unconfirmed cells stay visible as gaps.', 'compareoxygen'),

        'features_eyebrow'  => __('Feature glossary', 'compareoxygen'),
        'features_title'    => __('The specifications that change daily life', 'compareoxygen'),
        'features_intro'    => __('Marketing pages use the same words in different ways. Here is how we treat them when comparing.', 'compareoxygen'),

        'reviews_eyebrow'   => __('Featured reviews', 'compareoxygen'),
        'reviews_title'     => __('Full specification records, in plain English', 'compareoxygen'),
        'guides_eyebrow'    => __('Buying guides', 'compareoxygen'),
        'guides_title'      => __('Learn the questions before the shortlist', 'compareoxygen'),

        'method_eyebrow'    => __('Editorial methodology', 'compareoxygen'),
        'method_title'      => __('We would rather show a gap than invent a number', 'compareoxygen'),
        'method_body'       => __('Our editors collect manufacturer documentation, verify each fact against the source, and record the date. Only then does a specification appear on a comparison table.', 'compareoxygen'),

        'faq_eyebrow'       => __('Common questions', 'compareoxygen'),
        'faq_title'         => __('What readers ask before comparing', 'compareoxygen'),
        'faq_1_q'           => __('How do you verify the specifications on this site?', 'compareoxygen'),
        'faq_1_a'           => __('Each fact is entered with the manufacturer’s own product page, specification sheet, or manual as its source, plus the date our editor checked it. Facts without both a source and a verification date are shown publicly as “Not confirmed” rather than filled in from memory or estimated.', 'compareoxygen'),
        'faq_2_q'           => __('Have you personally tested every concentrator?', 'compareoxygen'),
        'faq_2_a'           => __('No. Most pages are based on manufacturer documentation and editorial analysis of published specifications. When a page involves direct use or measurement, we say so explicitly. We never imply hands-on testing where none occurred.', 'compareoxygen'),
        'faq_3_q'           => __('What does “Not confirmed” mean?', 'compareoxygen'),
        'faq_3_a'           => __('It means the manufacturer does not publish that figure, or our team has not yet verified it against an official source. We leave the value blank instead of guessing, converting, or copying an unsourced number from another website.', 'compareoxygen'),
        'faq_4_q'           => __('Can this site tell me which concentrator my doctor would recommend?', 'compareoxygen'),
        'faq_4_a'           => __('No. We publish general educational research, not medical advice. Your prescription, oxygen needs, activity level, and travel plans are clinical questions for your clinician and oxygen supplier. Our comparisons help you prepare for that conversation.', 'compareoxygen'),
        'faq_5_q'           => __('How does CompareOxygenConcentrators.com make money?', 'compareoxygen'),
        'faq_5_a'           => __('The site is owned by Oxygen Care Supply, a durable medical equipment supplier. We may earn revenue when readers choose a product through our links. Commercial relationships do not decide which specifications we publish, which models we compare, or which awards our editors assign. The full arrangement is described in our disclosure.', 'compareoxygen'),
    ];
}

/** Grouped field definitions used by the settings screen. */
function coc_settings_fields(): array {
    return [
        'Header' => [
            'utility_text'     => ['Utility bar text', 'text'],
            'header_cta_label' => ['Header button label', 'text'],
        ],
        'Footer' => [
            'footer_mission'    => ['Mission paragraph', 'textarea'],
            'footer_badge_1'    => ['Badge one', 'text'],
            'footer_badge_2'    => ['Badge two', 'text'],
            'footer_disclaimer' => ['Medical disclaimer', 'textarea'],
            'footer_copyright'  => ['Copyright line', 'text'],
        ],
        'Home: hero' => [
            'hero_eyebrow'    => ['Eyebrow', 'text'],
            'hero_title'      => ['Headline', 'text'],
            'hero_lede'       => ['Intro paragraph', 'textarea'],
            'hero_cta1_label' => ['Primary button label', 'text'],
            'hero_cta1_url'   => ['Primary button URL (blank = comparison tool)', 'text'],
            'hero_cta2_label' => ['Secondary button label', 'text'],
            'hero_cta2_url'   => ['Secondary button URL', 'text'],
            'hero_meta_1'     => ['Trust note one', 'text'],
            'hero_meta_2'     => ['Trust note two', 'text'],
        ],
        'Home: trust strip' => [
            'trust_1' => ['Item one', 'text'],
            'trust_2' => ['Item two', 'text'],
            'trust_3' => ['Item three', 'text'],
            'trust_4' => ['Item four', 'text'],
        ],
        'Home: top picks' => [
            'picks_eyebrow' => ['Eyebrow', 'text'],
            'picks_title'   => ['Heading', 'text'],
            'picks_intro'   => ['Intro', 'textarea'],
        ],
        'Home: categories' => [
            'cases_eyebrow' => ['Eyebrow', 'text'],
            'cases_title'   => ['Heading', 'text'],
            'cases_intro'   => ['Intro', 'textarea'],
        ],
        'Home: awards' => [
            'awards_eyebrow' => ['Eyebrow', 'text'],
            'awards_title'   => ['Heading', 'text'],
            'awards_intro'   => ['Intro', 'textarea'],
        ],
        'Home: how comparison works' => [
            'steps_eyebrow' => ['Eyebrow', 'text'],
            'steps_title'   => ['Heading', 'text'],
            'steps_intro'   => ['Intro', 'textarea'],
            'step1_title'   => ['Step one heading', 'text'],
            'step1_body'    => ['Step one text', 'textarea'],
            'step2_title'   => ['Step two heading', 'text'],
            'step2_body'    => ['Step two text', 'textarea'],
            'step3_title'   => ['Step three heading', 'text'],
            'step3_body'    => ['Step three text', 'textarea'],
        ],
        'Home: glossary' => [
            'features_eyebrow' => ['Eyebrow', 'text'],
            'features_title'   => ['Heading', 'text'],
            'features_intro'   => ['Intro', 'textarea'],
        ],
        'Home: reviews and guides' => [
            'reviews_eyebrow' => ['Reviews eyebrow', 'text'],
            'reviews_title'   => ['Reviews heading', 'text'],
            'guides_eyebrow'  => ['Guides eyebrow', 'text'],
            'guides_title'    => ['Guides heading', 'text'],
        ],
        'Home: methodology panel' => [
            'method_eyebrow' => ['Eyebrow', 'text'],
            'method_title'   => ['Heading', 'text'],
            'method_body'    => ['Paragraph', 'textarea'],
        ],
        'Home: FAQ' => [
            'faq_eyebrow' => ['Eyebrow', 'text'],
            'faq_title'   => ['Heading', 'text'],
            'faq_1_q'     => ['Question one', 'text'],
            'faq_1_a'     => ['Answer one', 'textarea'],
            'faq_2_q'     => ['Question two', 'text'],
            'faq_2_a'     => ['Answer two', 'textarea'],
            'faq_3_q'     => ['Question three', 'text'],
            'faq_3_a'     => ['Answer three', 'textarea'],
            'faq_4_q'     => ['Question four', 'text'],
            'faq_4_a'     => ['Answer four', 'textarea'],
            'faq_5_q'     => ['Question five', 'text'],
            'faq_5_a'     => ['Answer five', 'textarea'],
        ],
    ];
}

/** Read one editable value, falling back to the shipped default. */
function coc_opt(string $key): string {
    static $settings = null;
    if ($settings === null) {
        $stored = get_option('coc_settings', []);
        $settings = is_array($stored) ? $stored : [];
    }
    if (isset($settings[$key]) && $settings[$key] !== '') {
        return (string) $settings[$key];
    }
    $defaults = coc_default_settings();
    return isset($defaults[$key]) ? (string) $defaults[$key] : '';
}

/** Validate and persist the settings form. */
function coc_register_settings(): void {
    register_setting('coc_settings_group', 'coc_settings', [
        'type' => 'array',
        'sanitize_callback' => 'coc_sanitize_settings',
        'default' => [],
    ]);
}
add_action('admin_init', 'coc_register_settings');

function coc_sanitize_settings($input): array {
    $fields = coc_settings_fields();
    $current = get_option('coc_settings', []);
    $current = is_array($current) ? $current : [];
    $clean = $current;
    if (!is_array($input)) {
        return $clean;
    }
    foreach ($fields as $group) {
        foreach ($group as $key => $meta) {
            if (!array_key_exists($key, $input)) {
                continue;
            }
            $raw = wp_unslash((string) $input[$key]);
            $clean[$key] = $meta[1] === 'textarea'
                ? sanitize_textarea_field($raw)
                : sanitize_text_field($raw);
        }
    }
    return $clean;
}

function coc_settings_menu(): void {
    add_theme_page(
        __('Site Settings', 'compareoxygen'),
        __('Site Settings', 'compareoxygen'),
        'manage_options',
        'coc-settings',
        'coc_render_settings'
    );
}
add_action('admin_menu', 'coc_settings_menu');

function coc_render_settings(): void {
    if (!current_user_can('manage_options')) {
        return;
    }
    $defaults = coc_default_settings();
    $stored = get_option('coc_settings', []);
    $stored = is_array($stored) ? $stored : [];
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Site Settings', 'compareoxygen'); ?></h1>
        <p><?php esc_html_e('Editable copy for the header, footer and home page. Leave a field as it is to keep the shipped wording. Clear a field to fall back to the shipped wording.', 'compareoxygen'); ?></p>
        <form method="post" action="options.php">
            <?php settings_fields('coc_settings_group'); ?>
            <?php foreach (coc_settings_fields() as $group_label => $fields) : ?>
                <h2><?php echo esc_html($group_label); ?></h2>
                <table class="form-table" role="presentation">
                    <tbody>
                    <?php foreach ($fields as $key => $meta) :
                        $value = $stored[$key] ?? '';
                        $default = $defaults[$key] ?? '';
                        $id = 'coc-' . $key;
                        ?>
                        <tr>
                            <th scope="row"><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($meta[0]); ?></label></th>
                            <td>
                                <?php if ($meta[1] === 'textarea') : ?>
                                    <textarea id="<?php echo esc_attr($id); ?>" name="coc_settings[<?php echo esc_attr($key); ?>]" rows="4" class="large-text" placeholder="<?php echo esc_attr($default); ?>"><?php echo esc_textarea($value); ?></textarea>
                                <?php else : ?>
                                    <input type="text" id="<?php echo esc_attr($id); ?>" name="coc_settings[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($value); ?>" class="large-text" placeholder="<?php echo esc_attr($default); ?>">
                                <?php endif; ?>
                                <p class="description"><?php printf(esc_html__('Default: %s', 'compareoxygen'), esc_html(wp_trim_words($default, 18))); ?></p>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/** Sidebar and footer widget areas. */
function coc_register_sidebars(): void {
    register_sidebar([
        'name'          => __('Sidebar (reviews and pages)', 'compareoxygen'),
        'id'            => 'coc-sidebar',
        'description'   => __('Shown beside review and page content when at least one widget is added.', 'compareoxygen'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget__title">',
        'after_title'   => '</h2>',
    ]);
    register_sidebar([
        'name'          => __('Footer widgets', 'compareoxygen'),
        'id'            => 'coc-footer-widgets',
        'description'   => __('Extra row of widgets above the footer legal text.', 'compareoxygen'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget__title">',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'coc_register_sidebars');

/** True when the sidebar area has content to show. */
function coc_has_sidebar(): bool {
    return is_active_sidebar('coc-sidebar');
}
