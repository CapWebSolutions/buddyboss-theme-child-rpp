<?php

/**
 * Custom function to add referrals navigation items.
 * 
 */
if (!function_exists('custom_add_referrals_nav')) {
    function custom_add_referrals_nav()
    {
        if (is_user_logged_in() && ( ( bp_displayed_user_id() == get_current_user_id() ) || is_chapter_president() ) ) {

        // if (is_user_logged_in() && bp_displayed_user_id() == get_current_user_id()) {
            global $bp, $wpdb;

            $unseen_referrals_count = get_unseen_referrals_count();

            // Referrals
            bp_core_new_nav_item(
                array(
                    'name' => 'Referrals' . ($unseen_referrals_count > 0 ? ' <span class="count">' . $unseen_referrals_count . '</span>' : ''),
                    'slug' => 'referrals',
                    'position' => 100,
                    'screen_function' => 'custom_referrals_nav_screen',
                    'default_subnav_slug' => 'referrals',
                    'item_css_id' => 'referrals-nav',
                )
            );

            // My Referral History
            bp_core_new_nav_item(
                array(
                    'name' => 'My Sent Referrals',
                    'slug' => 'my-referral-history',
                    'position' => 100,
                    'screen_function' => 'custom_my_referral_history_nav_screen',
                    'default_subnav_slug' => 'my-referral-history',
                    'item_css_id' => 'my-referral-history-nav',
                )
            );

            // Manage Referrals - only available for users with role=chapter_president
            // if ($chapter_role === 'Lititz') {
            if ( is_chapter_president() ) {
                bp_core_new_nav_item(
                    array(
                        'name' => 'Manage Referrals',
                        'slug' => 'manage-referrals',
                        'position' => 100,
                        'screen_function' => 'custom_manage_referral_nav_screen',
                        'default_subnav_slug' => 'manage-referrals',
                        'item_css_id' => 'manage-referrals-nav',
                    )
                );
            }
        }
    }

    add_action('bp_setup_nav', 'custom_add_referrals_nav');
}

/**
 * Custom function to add referrals navigation items.
 */
if (!function_exists('custom_referrals_nav_screen')) {
    function custom_referrals_nav_screen()
    {
        if (is_user_logged_in() && bp_displayed_user_id() == get_current_user_id()) {

            update_referral_seen_status();

            add_action('bp_template_content', 'custom_referrals_nav_content');
            bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
        }
    }
}

/**
 * Custom function for displaying referral history navigation screen.
 * 
 */
if (!function_exists('custom_my_referral_history_nav_screen')) {
    function custom_my_referral_history_nav_screen()
    {
        if (is_user_logged_in() && bp_displayed_user_id() == get_current_user_id()) {
            add_action('bp_template_content', 'custom_my_referral_history_nav_content');
            bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
        }
    }
}

/**
 * Custom function for displaying manage referrals navigation screen.
 */
if (!function_exists('custom_manage_referral_nav_screen')) {
    function custom_manage_referral_nav_screen()
    {
        if (is_user_logged_in() && ( ( bp_displayed_user_id() == get_current_user_id() ) || is_chapter_president() ) ) {
            add_action('bp_template_content', 'custom_manage_referral_nav_content');
            bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
        }
    }
}

/**
 * Custom function for displaying manage members navigation screen.
 */
if (!function_exists('custom_manage_member_nav_screen')) {
    function custom_manage_member_nav_screen()
    {
        // if (is_user_logged_in() && bp_displayed_user_id() == get_current_user_id()) {
        if (is_user_logged_in() && ( ( bp_displayed_user_id() == get_current_user_id() ) || is_chapter_president() ) ) {
            add_action('bp_template_content', 'custom_manage_member_nav_content');
            bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
        }
    }
}

/**
 * Update the 'is_seen' column when the user visits the Referrals page.
 */
function update_referral_seen_status()
{
    if (is_user_logged_in() && bp_is_current_component('referrals')) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'show_referrals';
        $current_user_id = get_current_user_id();

        $wpdb->update(
            $table_name,
            array('is_seen' => 1),
            array('recipient_id' => $current_user_id, 'is_seen' => 0)
        );
    }
}


/**
 * Generate custom referrals navigation content.
 */
if (!function_exists('custom_referrals_nav_content')) {
    function custom_referrals_nav_content()
    {
        $html = generate_referral_html('sender_id');
        echo $html;

        if (isset($_POST['email_notifications_toggle'])) {
            $email_notifications_toggle = sanitize_text_field($_POST['email_notifications_toggle']);

            update_user_meta(get_current_user_id(), 'referral_email_notifications', $email_notifications_toggle);

            if ($email_notifications_toggle == 'on') {
                $subject = 'New Referral Received';
                $message = 'You have received a new referral. Log in to check it out!';
                $headers = array('Content-Type: text/html; charset=UTF-8');

                $email_sent = wp_mail(get_userdata(get_current_user_id())->user_email, $subject, $message, $headers);

                if ($email_sent) {
                    echo 'Email sent successfully.';
                } else {
                    echo 'Error sending email.';
                }
            }
        }
    }
}

/**
 * A function to generate custom referral history navigation content.
 */
if (!function_exists('custom_my_referral_history_nav_content')) {
    function custom_my_referral_history_nav_content()
    {
        $html = generate_referral_html('recipient_id');
        echo $html;
    }
}

/**
 * Generates HTML for the referral data based on the user type field.
 *
 * @param datatype $user_type_field description
 * @throws Some_Exception_Class description of exception
 * @return Some_Return_Value
 */
if (!function_exists('generate_referral_html')) {
    function generate_referral_html($user_type_field)
    {
        if (is_user_logged_in() && bp_displayed_user_id() == get_current_user_id()) {
            global $wpdb;

            $table_name = $wpdb->prefix . 'show_referrals';
            $current_user_id = get_current_user_id();

            $referral_data = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE recipient_id = %d ORDER BY ref_id DESC", $current_user_id), ARRAY_A);

            $referral_history_data = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE sender_id = %d ORDER BY ref_id DESC", $current_user_id), ARRAY_A);

            if (empty($referral_data) && empty($referral_history_data)) {
                return 'No Referrals Found';
            }

            $is_my_referral_history = bp_is_current_component('my-referral-history');

            $email_notifications_toggle = get_user_meta(get_current_user_id(), 'referral_email_notifications', true);
        ?>
            <div id="item-body" class="item-body">
                <div class="bp-profile-wrapper need-separator">
                    <div class="bp-profile-content">
                        <div class="profile public">
                            <div class="group-separator-block">
                                <header class="entry-header profile-loop-header profile-header flex align-items-center">
                                    <h1 class="entry-title bb-profile-title">
                                        <?php echo $is_my_referral_history ? 'My Referral History' : 'Referrals'; ?>
                                    </h1>

                                    <?php if (bp_is_current_component('referrals')) : ?>
                                        <div class="email-notifications-toggle">
                                            <label>Email Notifications</label>
                                            <label class="switch">
                                                <span class="switch_on">ON</span>
                                                <span class="switch_off">OFF</span>
                                                <input type="checkbox" id="emailNotificationsToggle" name="email_notifications_toggle" <?php echo ($email_notifications_toggle === 'on') ? 'checked' : ''; ?>>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    <?php endif; ?>
                                </header>
                                <div class="bp-widget profile">
                                    <div class="referral-list">
                                        <?php

                                        foreach ($is_my_referral_history ? $referral_history_data : $referral_data as $referral) {

                                            $sender_id = $referral['sender_id'];
                                            $recipient_id = $referral['recipient_id'];

                                            $sender_name = get_user_meta($sender_id, 'display_name', true);
                                            $recipient_name = get_user_meta($recipient_id, 'display_name', true);
                                            $recipient_image = bp_core_fetch_avatar(array('item_id' => $recipient_id, 'type' => 'full', 'html' => false));
                                            $sender_image = bp_core_fetch_avatar(array('item_id' => $sender_id, 'type' => 'full', 'html' => false));

                                            $user_image = $is_my_referral_history ? $recipient_image : $sender_image;

                                            if ($is_my_referral_history) {
                                                $profile_url = bp_core_get_user_domain($recipient_id);
                                            } else {
                                                $profile_url = bp_core_get_user_domain($sender_id);
                                            }

                                            $date_sent = date('F j, Y', strtotime($referral['sent_date']));

                                            $referral_name = $referral['ref_name'];
                                            $full_message = ($referral['ref_message'] !== null) ? ltrim($referral['ref_message']) : null;

                                            $phone_number = $referral['ref_phoneno'];
                                            $email_address = $referral['ref_email'];

                                            $label = $is_my_referral_history ? 'Referral Sent' : 'Referral Received';

                                        ?>
                                            <div class="referral-entry" data-referral-id="<?php echo esc_attr($referral['ref_id']); ?>">

                                                <div class="referral-entry-top">
                                                    <div class="referral-label">
                                                        <?php echo esc_html($label); ?>
                                                    </div>
                                                    <div class="date-sent">
                                                        <?php echo esc_html($date_sent); ?>
                                                    </div>
                                                    <div class="type-of-referral"><?php echo esc_html($referral['type_of_referral']); ?></div>
                                                </div>

                                                <div class="referral-entry-body">
                                                    <div class="user-img">
                                                        <a href="<?php echo esc_url($profile_url); ?>"><?php echo esc_html($is_my_referral_history ? $recipient_name : $sender_name); ?></a>

                                                        <div class="user-image">
                                                            <img src="<?php echo $user_image; ?>">
                                                        </div>

                                                    </div>

                                                    <div class="user-details">
                                                        <div class="name"><?php echo $referral_name; ?> </div>
                                                        <div class="contact-info">
                                                            <div class="phone-no"><a href="tel:<?php echo esc_attr($phone_number); ?>"><?php echo esc_html($phone_number); ?></a></div>
                                                            <div class="referral_mail"><a href="mailto:<?php echo esc_attr($email_address); ?>"><?php echo esc_html($email_address); ?></a></div>
                                                        </div>
                                                        <div class="message"><?php echo esc_html($full_message); ?></div>


                                                        <div class="delete-option">
                                                            <button class="delete-link" onclick="deleteReferral(<?php echo esc_attr($referral['ref_id']); ?>)">Delete</button>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        <?php } ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function deleteReferral(referralId) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>');
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            var deletedEntry = document.querySelector('.referral-entry[data-referral-id="' + referralId + '"]');
                            if (deletedEntry) {
                                deletedEntry.remove();
                            }
                        } else {
                            console.error('Error deleting referral entry');
                        }
                    };
                    xhr.send('action=delete_referral&referral_id=' + referralId);
                }

                jQuery(document).ready(function($) {
                    $('#emailNotificationsToggle').change(function() {
                        var isChecked = $(this).prop('checked');
                        $.ajax({
                            type: 'POST',
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            data: {
                                action: 'update_email_notifications_toggle',
                                toggle_status: isChecked ? 'on' : 'off'
                            },
                            success: function(response) {
                                console.log(response);
                            }
                        });
                    });
                });
            </script>
<?php
        }

        return '';
    }
}
