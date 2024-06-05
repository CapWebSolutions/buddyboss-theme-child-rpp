<?php
if (!function_exists('display_members_by_chapter_shortcode')) :

    function display_members_by_chapter_shortcode($atts)
    {
        $atts = shortcode_atts(
            array(
                'profile_type' => '', // Default profile types
            ),
            $atts,
            'display_members'
        );

        // Get all available profile types
        $profile_types      = bp_get_member_types(array(), 'objects');
        $profile_types_data = array();
        foreach ($profile_types as $profile_type) {
            if (!empty($profile_type->name)) {
                $profile_types_data[$profile_type->name] = $profile_type->labels['singular_name'];
            }
        }

        // Split the comma-separated list of profile types
        $requested_profile_types = array_map('trim', explode(',', $atts['profile_type']));

        // Check if any of the provided profile types are invalid
        foreach ($requested_profile_types as $requested_type) {
            if (!in_array($requested_type, array_keys($profile_types_data))) {
                return '<p>Invalid profile type specified: ' . $requested_type . '</p>';
            }
        }

        // Initialize output
        $output = '';

        // Display header section only once
        $output .= '<div class="bb-block-header flex align-items-center">
                        <div class="bb-block-header__title">
                            <h3>Members</h3>
                        </div>
                        <div class="bb-block-header__extra push-right">
                            <a href="' . bp_get_members_directory_permalink() . '" class="count-more">All Members<i class="bb-icon-l bb-icon-angle-right"></i></a>
                        </div>
                    </div>';

        // Loop through each requested profile type
        foreach ($requested_profile_types as $profile_type) {
            // Get members based on the specified profile type
            $members = array(
                'member_type' => $profile_type,
            );

            // Check if users are found
            if (bp_has_members($members)) {
                ob_start();
                ?>
                <div class="bb-members new-member-list">
                    <div class="bbel-list-flow">
                        <div class="bb-members-list bb-members-list--active bb-members-list--align-left">
                            <?php while (bp_members()) : bp_the_member();
                                $user_id = bp_get_member_user_id();
                                $business = bp_get_profile_field_data(array('field' => 4, 'user_id' => $user_id));
                                $category = bp_get_profile_field_data(array('field' => 46, 'user_id' => $user_id));
                                $chapter_role = bp_get_profile_field_data(array('field' => 40, 'user_id' => $user_id));
                            ?>
                                <div class="bb-members-list__item">
                                    <div class="bb-members-list__avatar">
                                        <a href="<?php bp_member_permalink(); ?>">
                                            <?php bp_member_avatar('type=thumb&width=96&height=96'); ?>
                                        </a>
                                    </div>

                                    <div class="bb-members-list__name fn">
                                        <a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>
                                    </div>
                                    <!-- Display extra_member_info here -->

                                    <div class="extra-member-info">
                                        <p class="member_business"><?php echo $business; ?></p>
                                        <p class="member_category"><?php echo $category; ?></p>
                                        <p class="member_chapter"><?php echo $chapter_role; ?></p>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
                <?php
                $output .= ob_get_clean();
            } else {
                $output .= '<p>No members found for the specified profile type: ' . $profile_types_data[$profile_type] . '</p>';
            }
        }

        return $output;
    }

    // Register the shortcode
    add_shortcode('display_members', 'display_members_by_chapter_shortcode');

endif;
?>
