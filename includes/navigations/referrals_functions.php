<?php

/**
 * Get the count of unseen referrals for the current user.
 *
 * @return int The count of unseen referrals.
 */
function get_unseen_referrals_count()
{
    if (is_user_logged_in()) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'show_referrals';
        $current_user_id = get_current_user_id();

        $unseen_referrals_count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE recipient_id = %d AND is_seen = 0",
                $current_user_id
            )
        );

        return $unseen_referrals_count;
    }

    return 0;
}

/**
 * Deletes a referral callback if the referral ID is set in the POST data.
 *
 * @throws Exception if the referral ID is not set
 */
add_action('wp_ajax_delete_referral', 'delete_referral_callback');
function delete_referral_callback()
{
    if (isset($_POST['referral_id'])) {
        $referral_id = intval($_POST['referral_id']);
        global $wpdb;
        $table_name = $wpdb->prefix . 'show_referrals';

        // Use delete method to delete the data from the table
        $result = $wpdb->delete($table_name, array('ref_id' => $referral_id));

        if ($result === false) {
            // Log or display an error message
            error_log('Error deleting referral with ID: ' . $referral_id);
        }

        wp_die();
    }
}

/**
 * Updates the email notifications toggle status for the current user.
 *
 * @throws Some_Exception_Class description of exception
 */
add_action('wp_ajax_update_email_notifications_toggle', 'update_email_notifications_toggle_callback');
function update_email_notifications_toggle_callback()
{
    // if (isset($_POST['toggle_status'])) {
        $toggle_status = sanitize_text_field($_POST['toggle_status']);
        $toggle_status = 'on';   // Debug force to true
        update_user_meta(get_current_user_id(), 'referral_email_notifications', $toggle_status);
        wp_die();
    // }
}

/**
 * Retrieves referral data for a specific user from the database.
 *
 * @param int $user_id The ID of the user to retrieve referral data for.
 * @return array The referral data for the specified user.
 */
function get_referral_data($user_id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'show_referrals';

    $referral_data = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE sender_id = %d",
            $user_id
        ),
        ARRAY_A
    );

    if ($wpdb->last_error) {
        // Display the database error for debugging
        echo 'Database Error: ' . $wpdb->last_error;
    }

    return $referral_data;
}

/**
 * Counts the number of referrals sent in the current month.
 *
 * @param array $referral_data The array of referral data
 * @return int The count of referrals sent in the current month
 */
function count_referrals_this_month($referral_data)
{
    $current_month = date('m');
    $referrals_count = 0;

    foreach ($referral_data as $referral) {
        $sent_month = date('m', strtotime($referral['sent_date']));
        if ($sent_month === $current_month) {
            $referrals_count++;
        }
    }

    return $referrals_count;
}

/**
 * Counts the number of referrals sent in the current year.
 *
 * @param array $referral_data The array of referral data
 * @return int The count of referrals sent in the current year
 */
function count_referrals_this_year($referral_data)
{
    $current_year = date('Y');
    $referrals_count = 0;

    foreach ($referral_data as $referral) {
        $sent_year = date('Y', strtotime($referral['sent_date']));
        if ($sent_year === $current_year) {
            $referrals_count++;
        }
    }

    return $referrals_count;
}
