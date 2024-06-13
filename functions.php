<?php

/**
 * @package BuddyBoss Child
 * The parent theme functions are located at /buddyboss-theme/inc/theme/functions.php
 * Add your own functions at the bottom of this file.
 */


/****************************** THEME SETUP ******************************/
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

if (!defined('_S_VERSION')) {
  // Replace the version number of the theme on each release.
  define('_S_VERSION', '1.0.12');
}

/**
 * Sets up theme for translation
 *
 * @since BuddyBoss Child 1.0.0
 */
function buddyboss_theme_child_languages()
{
  /**
   * Makes child theme available for translation.
   * Translations can be added into the /languages/ directory.
   */

  // Translate text from the PARENT theme.
  load_theme_textdomain('buddyboss-theme', get_stylesheet_directory() . '/languages');

  // Translate text from the CHILD theme only.
  // Change 'buddyboss-theme' instances in all child theme files to 'buddyboss-theme-child'.
  // load_theme_textdomain( 'buddyboss-theme-child', get_stylesheet_directory() . '/languages' );

}
add_action('after_setup_theme', 'buddyboss_theme_child_languages');

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Boss Child Theme  1.0.0
 */
function buddyboss_theme_child_scripts_styles()
{
  /**
   * Scripts and Styles loaded by the parent theme can be unloaded if needed
   * using wp_deregister_script or wp_deregister_style.
   *
   * See the WordPress Codex for more information about those functions:
   * http://codex.wordpress.org/Function_Reference/wp_deregister_script
   * http://codex.wordpress.org/Function_Reference/wp_deregister_style
   **/

  // Styles
  wp_enqueue_style('buddyboss-child-css', get_stylesheet_directory_uri() . '/assets/css/custom.css', '', _S_VERSION, 'all');

  // Javascript
  wp_enqueue_script('buddyboss-child-js', get_stylesheet_directory_uri() . '/assets/js/custom.js', array('jquery'), time(), true);

  // Localize variables for AJAX
  wp_localize_script('buddyboss-child-js', 'MyAjax', array(
    'ajaxurl' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('my_nonce'),
    'approve_member_nonce' => wp_create_nonce('approve_member_ajax_nonce'),
  ));
}
add_action('wp_enqueue_scripts', 'buddyboss_theme_child_scripts_styles', 9999);


// Enqueue custom admin stylesheet
function enqueue_custom_admin_style()
{
  wp_enqueue_style('custom-admin-style', get_stylesheet_directory_uri() . '/assets/css/admin-custom.css');
}
add_action('admin_enqueue_scripts', 'enqueue_custom_admin_style', 9999);


/****************************** CUSTOM FUNCTIONS ******************************/

// Add your own custom functions here

/**
 * include file
 */
/**
 * Add action hooks to display member details in BuddyPress members loop
 */
function custom_display_member_details()
{
  // First Name
  echo '<p>' . bp_get_profile_field_data('field=First Name') . '</p>';
  // Last Name
  echo '<p>' . bp_get_profile_field_data('field=Last Name') . '</p>';
  // Business
  echo '<p>' . bp_get_profile_field_data('field=Business') . '</p>';
  // Chapter Role
  echo '<p>' . bp_get_profile_field_data('field=Chapter Role') . '</p>';
}
add_action('bp_directory_members_item', 'custom_display_member_details');


/**
 * include files
 */
include_once get_stylesheet_directory() . '/includes/save_referral_data.php';
include_once get_stylesheet_directory() . '/includes/display_referral_data.php';
include_once get_stylesheet_directory() . '/includes/member_shortcode.php';



/**
 * fuction to show table data
 */
add_action('wp_footer', 'check_table_existence');

if (!function_exists('check_table_existence')) :
  function check_table_existence()
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'show_referrals';

    // Retrieve all data from the custom table
    $referral_data = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

    // Attempt to get distinct meta keys with error handling
    $user_id = 1;

    $user_meta_keys = bp_get_profile_field_data($user_id);

    $column_names = $wpdb->get_col("DESC {$table_name}");
    // Display the column names
    // 	  echo "Columns of table '{$table_name}':<br>";
    // 	  foreach ($column_names as $column_name) {
    // 		  echo $column_name . "<br>";
    // 	  }

    $tables = $wpdb->get_results("SHOW TABLES", ARRAY_N);
    //     	  pr($referral_data);
  }

endif;



if (!function_exists('pr')) {
  function pr($data, $exit = false)
  {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    if ($exit) {
      exit($exit);
    }
  }
}
function custom_login_logo()
{
  echo '<style>
    body.login.login-split-page #login {
        width: 270px;
    }
  </style>';
}


/**
 * Add the "Received Referral" option
 *
 * @param array $situations
 * @return array
 */
if (!function_exists('add_received_referral_situation')) :
  function add_received_referral_situation($situations)
  {
    // Add the "Received Referral" option
    $situations['received-referral'] = array(
      'label' => __('Received Referral', 'buddyboss'),
      'value' => 'referrals-received',
    );

    return $situations;
  }
  add_filter('buddyboss_situations_filter', 'add_received_referral_situation');
endif;


/**
 * Adds a custom checkbox to the specified BuddyPress email type taxonomy.
 *
 * @param array $args The arguments for the taxonomy.
 * @param int $post_id The post ID.
 * @return array The modified arguments for the taxonomy.
 */
if (!function_exists('custom_add_checkbox_to_bp_email_type')) :
  function custom_add_checkbox_to_bp_email_type($args, $post_id)
  {
    // Check if the taxonomy is 'bp-email-type'
    if ($args['taxonomy'] === 'bp-email-type') {
      // Add your custom checkbox HTML here
      $args['walker'] = new Custom_BP_Walker_Category_Checklist();
    }

    return $args;
  }
endif;

// Check if the class Custom_BP_Walker_Category_Checklist does not exist
if (!class_exists('Custom_BP_Walker_Category_Checklist') && class_exists('BP_Walker_Category_Checklist')) :
  class Custom_BP_Walker_Category_Checklist extends BP_Walker_Category_Checklist
  {

    /**
     * start_el function description.
     *
     * @param datatype &$output description
     * @param datatype $category description
     * @param datatype $depth description
     * @param datatype $args description
     * @param datatype $id description
     */

    public function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0)
    {
      $category_id = $category->term_id;
      // Add your custom checkbox HTML only for the specific category
      if ($category->slug === 'received-referral') {
        $output .= '<li id="bp-email-type-id-' . $category_id . '">';
        $output .= '<label><input type="checkbox" id="in-bp-email-type-' . $category_id . '" name="tax_input[bp-email-type][]" value="' . esc_attr($category->slug) . '"> Received Referral</label>';
        $output .= '</li>';
      }

      // Output the default checkbox HTML for the current category
      parent::start_el($output, $category, $depth, $args, $id);
    }

    public function end_el(&$output, $page, $depth = 0, $args = array())
    {
      // Add your custom checkbox HTML here
    }
  }

  // Add filter to modify the arguments for wp_terms_checklist
  add_filter('wp_terms_checklist_args', 'custom_add_checkbox_to_bp_email_type', 10, 2);
endif;




/**
 * Custom function to filter and modify BuddyPress member display
 * @param array $args Arguments for the member display filter.
 * @return bool
 */
if (!function_exists('custom_member_display_filter')) :
  function custom_member_display_filter($args)
  {
    // Set default query parameters using bp_parse_args
    $args = bp_parse_args(
      array(
        'type' => 'active',     // Set the member type to 'active'
        'per_page' => 20,       // Set the number of members per page to 20
      ),
      $args
    );

    // Query members based on the provided arguments
    $members = new BP_User_Query($args);

    // Check if members are found
    if (!empty($members->results)) {
      // Loop through each member and fetch additional profile information
      foreach ($members->results as &$member) {
        $user_id = $member->ID;

        // Example: Get business information
        $business = bp_get_profile_field_data(array('field' => '4', 'user_id' => $user_id));

        // Example: Get category information
        $category = bp_get_profile_field_data(array('field' => '46', 'user_id' => $user_id));

        // Example: Get chapter role information
        $chapter_role = bp_get_profile_field_data(array('field' => '40', 'user_id' => $user_id));

        // Add the additional information to the member object
        $member->extra_member_info = array(
          'business' => $business,
          'category' => $category,
          'chapter_role' => $chapter_role,
        );
      }

      // Access the global members template
      global $members_template;

      // Check if the members template exists and is an object
      if (isset($members_template->members)) {
        // Loop through each template member and add extra information
        foreach ($members_template->members as &$template_member) {
          $user_id = isset($template_member->ID) ? $template_member->ID : 0;

          // Find the corresponding modified member data
          $modified_member = array_filter($members->results, function ($m) use ($user_id) {
            return isset($m->ID) && $m->ID == $user_id;
          });

          // If a match is found, merge the extra information into the template member
          if (!empty($modified_member)) {
            $modified_member = reset($modified_member);
            $template_member->extra_member_info = $modified_member->extra_member_info;
          }
        }
      }
    }

    // Return true to indicate that members are found
    return true;
  }
endif;

// Add the custom member display filter to BuddyPress members query
add_filter('bp_has_members', 'custom_member_display_filter');

// Hook into the pending_to_active transition
add_action('bp_core_activated_user', 'update_user_role_on_pending_to_active', 10, 3);

function update_user_role_on_pending_to_active($user_id, $key, $user)
{
  // Check if the user status transitioned from pending to active
  if ($user['meta']['field_11'] === 'Lititz') {

    $user_role = 'lititz_chapter_president';

    // Update the user's role
    $user = new WP_User($user_id);
    $user->set_role($user_role);
  }
}

// Add AJAX action for saving referral data
add_action('wp_ajax_approve_member_ajax', 'approve_member_ajax');
add_action('wp_ajax_nopriv_approve_member_ajax', 'approve_member_ajax');

function approve_member_ajax()
{
  if (!wp_verify_nonce($_POST['nonce'], "approve_member_ajax_nonce")) {
    exit("Direct request not allowed");
  }

  $user_id = $_POST['user_id'];

  $active = activate_pending_buddypress_user($user_id);
  // pr($active);
  // Check if user is actually pending
  // if ( ! $signup || ! $signup->activation_key ) {
  //   return false; // Not a pending user
  // }
}


function activate_pending_buddypress_user( $user_id ) {
  // Get the user data
  $user = get_userdata( $user_id );


  // Check if the user has a 'pending' status
  if ( 'pending' === $user->user_status ) {
      // Change the user status to 'active'
      $user->user_status = 'active';

      // Update the user data
      wp_update_user( $user );
  }
}
add_action( 'bp_core_activated_user', 'activate_pending_buddypress_user' );



/**
 * Get list of active chapters for use in Manage Referrals screens. 
 *
 * @param array $unique_chapters
 * @return array $unique_chapters 
 */
if ( !function_exists('get_active_chapter_list') ) {
  function get_active_chapter_list( $unique_chapters ){
    global $bp, $wpdb;

    $user_id = bp_displayed_user_id();
    $chapters = array();
    // Save current user displayed as default.
    $default_chapter = bp_get_profile_field_data(array('field' => 11, 'user_id' => $user_id));

    $users = get_users( 'fields=ID' ); // Retrieve all user ids
    foreach ($users as $user) {
        $chapter_name = bp_get_profile_field_data(array('field' => 11, 'user_id' => $user));
        if (!empty($chapter_name)) {
            $chapters[] = $chapter_name; // Add unique to the list
        }
    }

    $unique_chapters = array_unique($chapters); // Remove duplicates
    sort($unique_chapters); // Sort unique in ascending order
    array_splice( $unique_chapters, 0, 0, $default_chapter ); // Stuff default in at beginning 

    return $unique_chapters;

  };
}

/**
 * Is current user a chapter president? 
 *
 * @return boolean 
 */
if ( !function_exists('is_chapter_president') ) {
  function is_chapter_president() {
    $user_data = get_userdata( get_current_user_id() );
    $user_roles = $user_data->roles;
    // Determine if user role is a chapter president
    if (!empty($user_roles) && str_contains(implode(', ', $user_roles),'chapter_president') ) {
      return true;
    }
    return false;
  }
}

/**
 * Retrieve list of user ids belonging to selected chapter. 
 *
 * @param string $selected_chapter The alpha name of the chapter of interest.
 * @return array             An array of user ids associated with members in $selected_chapter.
 */

 function userids_in_chapter( $selected_chapter ) {
  $chapter_user_ids = [];
  // Retrieve all user ids
  $users = get_users( 'fields=ID' ); 

  foreach ($users as $user) {
    $user_chapter = bp_get_profile_field_data(array('field' => 11, 'user_id' => $user->ID));
    if ( $selected_chapter === $user_chapter ) {
      // Add user id to the list if their chapter is the one we are looking for
      $chapter_user_ids[] = $user; 
    }
  }
  sort( $chapter_user_ids, SORT_NUMERIC ); 
  return $chapter_user_ids;
}
