<?php
/**
 * Standard page.
 *
 * @package compareoxygen
 */

get_header();

while (have_posts()) :
    the_post();
    $page_id = get_the_ID();
    $slug = get_post_field('post_name', $page_id);
    $legal_slugs = ['privacy-policy', 'terms', 'disclosure', 'accessibility'];
    $is_legal = in_array($slug, $legal_slugs, true);
    $is_contact = $slug === 'contact';
    $prepared = compareoxygen_prepare_content(apply_filters('the_content', get_the_content()));
    $toc = $prepared['toc'];
    if ($is_contact) { $toc = []; }

    if ($is_legal || $is_contact) {
        $eyebrow = $is_contact ? __('Contact the editors', 'compareoxygen') : __('Site information', 'compareoxygen');
    } elseif (in_array($slug, ['how-we-rate', 'editorial-policy'], true)) {
        $eyebrow = __('Editorial standards', 'compareoxygen');
    } else {
        $eyebrow = __('About the project', 'compareoxygen');
    }
    ?>
    <main id="main-content">
        <?php compareoxygen_breadcrumbs(); ?>

        <section class="page-hero page-hero--mint">
            <div class="container page-hero__inner">
                <p class="eyebrow"><?php echo esc_html($eyebrow); ?></p>
                <h1><?php the_title(); ?></h1>
                <?php if (has_excerpt()) : ?>
                    <p class="page-hero__lede"><?php echo esc_html(get_the_excerpt()); ?></p>
                <?php endif; ?>
                <div class="page-hero__meta">
                    <span><?php echo compareoxygen_icon('clock'); ?><?php printf(esc_html__('Updated %s', 'compareoxygen'), esc_html(get_the_modified_date('F j, Y'))); ?></span>
                    <span><?php echo compareoxygen_icon('shield'); ?><?php esc_html_e('CompareOxygenConcentrators.com editorial team', 'compareoxygen'); ?></span>
                </div>
            </div>
        </section>

        <?php if ($toc) : ?>
            <div class="container guide-layout">
                <div>
                    <?php compareoxygen_toc($toc, coc_compare_tool_url(), __('Compare concentrators', 'compareoxygen')); ?>
                    <?php if (!is_page('how-we-rate')) : ?>
                        <div class="toc__cta" style="margin-top:1rem;">
                            <p><?php esc_html_e('Looking for the evidence?', 'compareoxygen'); ?></p>
                            <a class="btn btn--secondary btn--sm btn--block" href="<?php echo esc_url(compareoxygen_page_url('how-we-rate')); ?>"><?php esc_html_e('How we rate', 'compareoxygen'); ?></a>
                        </div>
                    <?php endif; ?>
                    <?php if (coc_has_sidebar()) : ?>
                        <div class="sidebar-widgets"><?php dynamic_sidebar('coc-sidebar'); ?></div>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if (!$is_legal) { compareoxygen_disclosure(); } ?>
                    <article class="prose"><?php echo wp_kses_post($prepared['content']); ?></article>
                    <?php if (!$is_legal) { compareoxygen_medical_note(); } ?>
                </div>
            </div>
        <?php else : ?>
            <div class="container legal-layout">
                <div>
                    <?php if (!$is_legal) { compareoxygen_disclosure(); } ?>
                    <article class="prose"><?php echo wp_kses_post($prepared['content']); ?></article>

                    <?php if ($is_contact) : ?>
                        <?php
                        $state = isset($_GET['contact']) ? sanitize_key(wp_unslash($_GET['contact'])) : '';
                        $messages = [
                            'success' => [__('Message received', 'compareoxygen'), __('Thank you — your message reached our editors. We aim to reply within three business days. If your question is clinical, please contact your clinician or oxygen supplier directly.', 'compareoxygen')],
                            'invalid' => [__('Check the form', 'compareoxygen'), __('Please add your name, a valid email address, and a message of at least 20 characters.', 'compareoxygen')],
                            'error' => [__('Could not send', 'compareoxygen'), __('The security check failed before the message was stored. Please reload the page and try again.', 'compareoxygen')],
                        ];
                        ?>
                        <section id="contact-form" class="section--tight">
                            <?php if (isset($messages[$state])) : ?>
                                <div class="notice <?php echo $state === 'success' ? '' : 'notice--warn'; ?>" role="status">
                                    <p><strong><?php echo esc_html($messages[$state][0]); ?></strong> <?php echo esc_html($messages[$state][1]); ?></p>
                                </div>
                            <?php endif; ?>

                            <div class="section-head section-head--split-top">
                                <h2 class="section-title"><?php esc_html_e('Send a message', 'compareoxygen'); ?></h2>
                                <p class="section-intro"><?php esc_html_e('Use this form for factual corrections, accessibility problems, or general research questions. We cannot answer individual medical questions or advise on a prescription.', 'compareoxygen'); ?></p>
                            </div>

                            <form class="contact-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                                <input type="hidden" name="action" value="coc_contact">
                                <?php wp_nonce_field('coc_contact', 'coc_contact_nonce'); ?>
                                <p class="form-honeypot" aria-hidden="true">
                                    <label for="contact-website"><?php esc_html_e('Website', 'compareoxygen'); ?></label>
                                    <input type="text" id="contact-website" name="website" tabindex="-1" autocomplete="off">
                                </p>
                                <div class="form-grid">
                                    <p class="form-field">
                                        <label for="contact-name"><?php esc_html_e('Your name', 'compareoxygen'); ?></label>
                                        <input type="text" id="contact-name" name="name" required autocomplete="name">
                                    </p>
                                    <p class="form-field">
                                        <label for="contact-email"><?php esc_html_e('Email address', 'compareoxygen'); ?></label>
                                        <input type="email" id="contact-email" name="email" required autocomplete="email">
                                    </p>
                                </div>
                                <p class="form-field">
                                    <label for="contact-subject"><?php esc_html_e('What is this about?', 'compareoxygen'); ?></label>
                                    <select id="contact-subject" name="subject">
                                        <option value="correction"><?php esc_html_e('Factual correction', 'compareoxygen'); ?></option>
                                        <option value="general"><?php esc_html_e('General question', 'compareoxygen'); ?></option>
                                        <option value="accessibility"><?php esc_html_e('Accessibility issue', 'compareoxygen'); ?></option>
                                        <option value="partnership"><?php esc_html_e('Partnership enquiry', 'compareoxygen'); ?></option>
                                    </select>
                                </p>
                                <p class="form-field">
                                    <label for="contact-message"><?php esc_html_e('Message', 'compareoxygen'); ?></label>
                                    <textarea id="contact-message" name="message" rows="7" required minlength="20"></textarea>
                                </p>
                                <p class="form-note"><?php esc_html_e('We store your message on this site and email a copy to our editorial team. Please do not include medical records, prescription details, or other sensitive health information.', 'compareoxygen'); ?></p>
                                <button class="btn btn--primary" type="submit"><?php echo compareoxygen_icon('mail'); ?><?php esc_html_e('Send message', 'compareoxygen'); ?></button>
                            </form>
                        </section>
                    <?php endif; ?>

                    <?php if (!$is_legal && !$is_contact) { compareoxygen_medical_note(); } ?>
                </div>

                <aside class="legal-aside">
                    <h2><?php esc_html_e('Editorial links', 'compareoxygen'); ?></h2>
                    <ul>
                        <li><a href="<?php echo esc_url(compareoxygen_page_url('how-we-rate')); ?>"><?php esc_html_e('How we rate', 'compareoxygen'); ?></a></li>
                        <li><a href="<?php echo esc_url(compareoxygen_page_url('editorial-policy')); ?>"><?php esc_html_e('Editorial policy', 'compareoxygen'); ?></a></li>
                        <li><a href="<?php echo esc_url(compareoxygen_page_url('disclosure')); ?>"><?php esc_html_e('Affiliate disclosure', 'compareoxygen'); ?></a></li>
                        <li><a href="<?php echo esc_url(compareoxygen_page_url('about')); ?>"><?php esc_html_e('About us', 'compareoxygen'); ?></a></li>
                        <li><a href="<?php echo esc_url(compareoxygen_page_url('contact')); ?>"><?php esc_html_e('Contact', 'compareoxygen'); ?></a></li>
                    </ul>
                    <div class="toc__cta" style="margin-top:1rem;">
                        <p><?php esc_html_e('Ready to compare?', 'compareoxygen'); ?></p>
                        <a class="btn btn--primary btn--sm btn--block" href="<?php echo esc_url(coc_compare_tool_url()); ?>"><?php echo compareoxygen_icon('scale'); ?><?php esc_html_e('Open the tool', 'compareoxygen'); ?></a>
                    </div>
                    <?php if (coc_has_sidebar()) : ?>
                        <div class="sidebar-widgets"><?php dynamic_sidebar('coc-sidebar'); ?></div>
                    <?php endif; ?>
                </aside>
            </div>
        <?php endif; ?>
    </main>
    <?php
endwhile;

get_footer();
