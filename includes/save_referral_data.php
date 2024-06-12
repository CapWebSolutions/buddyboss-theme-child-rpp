<?php
// Add AJAX action for saving referral data
add_action('wp_ajax_save_referral_data', 'save_referral_data');
add_action('wp_ajax_nopriv_save_referral_data', 'save_referral_data');

/**
 * Save referral data into the custom table.
 *
 */
if (!function_exists('save_referral_data')) {

    function save_referral_data()
    {
        global $wpdb;

        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'my_nonce')) {
            wp_send_json_error('Permission check failed.');
        }

        // Get form data
        $referral_data = $_POST['formData'];

        // validation
        if (isset($_POST['formData'])) :
            $name = (isset($_POST["ref-name"]) && $_POST["ref-name"] != '') ? $_POST["ref-name"] : false;
            $phone = (isset($_POST["ref-phoneno"]) && $_POST["ref-phoneno"] != '') ? $_POST["ref-phoneno"] : false;
            $email = (isset($_POST["ref-email"]) && $_POST["ref-email"] != '') ? $_POST["ref-email"] : false;
            $message = (isset($_POST["ref-message"]) && $_POST["ref-message"] != '') ? $_POST["ref-message"] : false;
            $type_of_referral = isset($referral_data['type_of_referral']) ? $referral_data['type_of_referral'] : '';

            // Validate the fields
            $errors = array();

            if ($name) {
                $errors["ref-name"] = "Referral’s Full Name is required";
            } else if ($phone) {
                $errors["ref-phoneno"] = "Referral’s Phone Number is required";
            } else if ($email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors["ref-email"] = "Valid Referral’s Email is required";
            } else if ($message) {
                $errors["ref-message"] = "Message is required";
            } else if ($type_of_referral) {
                $errors["type_of_referral"] = "Type of Referral is required";
            } else {
                // Return success as JSON
                wp_send_json_success('please enter valid data.');
                exit;
            }

        else :
            // Return errors as JSON
            wp_send_json_error($errors);

        endif;

        // Get the current logged-in user ID
        $sender_id = get_current_user_id();
        $sender_chapter = get_buddyboss_profile_data($sender_id);
        var_dump($sender_chapter);

        // Get the current DISPLAYED user's ID and chapter. This will be the recipient of the referral. 
        $current_displayed_user_id = bp_displayed_user_id();
        $displayed_user_chapter = bp_get_profile_field_data(array('field' => 11, 'user_id' => $current_displayed_user_id));
        var_dump($displayed_user_chapter);

        // Insert data into custom table with sender and recipient ID
        $table_name = $wpdb->prefix . 'show_referrals';

        $wpdb->insert(
            $table_name,
            array(
                'ref_name' => $referral_data['ref_name'],
                'ref_email' => $referral_data['ref_email'],
                'ref_phoneno' => $referral_data['ref_phoneno'],
                'ref_message' => $referral_data['ref_message'],
                'type_of_referral' => $referral_data['type_of_referral'],
                'sender_id' => $sender_id,
                'recipient_id' => $referral_data['ref_recipient_id'],
                'sent_date' => current_time('mysql', 1), // Add the current timestamp for the sent date
                'received_date' => current_time('mysql', 1), // Add the current timestamp for the received date
            ),
            array('%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s')
        );




        // Return a JSON response with the success message
        wp_send_json_success('Referral sent successfully.');
    }
}
