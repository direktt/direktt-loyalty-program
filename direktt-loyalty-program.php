<?php

/**
 * Plugin Name: Direktt Loyalty Program
 * Description: Direktt Loyalty Program Direktt Plugin
 * Version: 1.0.0
 * Author: Direktt
 * Author URI: https://direktt.com/
 * License: GPL2
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'plugins_loaded', 'direktt_loyalty_program_activation_check', -20 );

function direktt_loyalty_program_activation_check() {
    if ( ! function_exists( 'is_plugin_active' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $required_plugin = 'direktt-plugin/direktt.php';

    if ( ! is_plugin_active( $required_plugin ) ) {
        add_action(
            'after_plugin_row_direktt-loyalty-program/direktt-loyalty-program.php',
            function ( $plugin_file, $plugin_data, $status ) {
				$colspan = 3;
				?>
            <tr class="plugin-update-tr">
                <td colspan="<?php echo esc_attr( $colspan ); ?>" style="box-shadow: none;">
                    <div style="color: #b32d2e; font-weight: bold;">
                        <?php echo esc_html__( 'Direktt Loyalty Program requires the Direktt WordPress Plugin to be active. Please activate Direktt WordPress Plugin first.', 'direktt-loyalty-program' ); ?>
                    </div>
                </td>
            </tr>
				<?php
			},
            10,
            3
        );

        deactivate_plugins( plugin_basename( __FILE__ ) );
    }
}

add_action( 'direktt_setup_settings_pages', 'setup_loyalty_program_settings_page' );

function setup_loyalty_program_settings_page() {
    Direktt::add_settings_page(
        array(
            'id'       => 'loyalty-program',
            'label'    => __( 'Loyalty Program Settings', 'direktt-loyalty-program' ),
            'callback' => 'render_loyalty_program_settings',
            'priority' => 2,
        )
    );
}

function render_loyalty_program_settings() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'direktt-loyalty-program' ) );
    }

    $success = false;

    // Handle form submission
    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['direktt_admin_loyalty_program_nonce'] ) && wp_verify_nonce( $_POST['direktt_admin_loyalty_program_nonce'], 'direktt_admin_loyalty_program_save' ) ) {
        // update options based on form submission
        update_option( 'direktt_loyalty_program_categories', isset( $_POST['direktt_loyalty_program_categories'] ) ? intval( $_POST['direktt_loyalty_program_categories'] ) : 0 );
        update_option( 'direktt_loyalty_program_tags', isset( $_POST['direktt_loyalty_program_tags'] ) ? intval( $_POST['direktt_loyalty_program_tags'] ) : 0 );
        update_option( 'direktt_loyalty_program_initial_points', intval( $_POST['direktt_loyalty_program_initial_points'] ) );
        update_option( 'direktt_loyalty_points_rules', array_map( 'intval', $_POST['direktt_loyalty_points_rules'] ?? array() ) );
        update_option( 'direktt_loyalty_user', isset( $_POST['direktt_loyalty_user'] ) ? 'yes' : 'no' );
        update_option( 'direktt_loyalty_user_template', intval( $_POST['direktt_loyalty_user_template'] ) );
        update_option( 'direktt_loyalty_admin', isset( $_POST['direktt_loyalty_admin'] ) ? 'yes' : 'no' );
        update_option( 'direktt_loyalty_admin_template', intval( $_POST['direktt_loyalty_admin_template'] ) );
        update_option( 'direktt_loyalty_user_reset', isset( $_POST['direktt_loyalty_user_reset'] ) ? 'yes' : 'no' );
        update_option( 'direktt_loyalty_user_template_reset', intval( $_POST['direktt_loyalty_user_template_reset'] ) );
        update_option( 'direktt_loyalty_admin_reset', isset( $_POST['direktt_loyalty_admin_reset'] ) ? 'yes' : 'no' );
        update_option( 'direktt_loyalty_admin_template_reset', intval( $_POST['direktt_loyalty_admin_template_reset'] ) );
        $success = true;
    }

    // Load stored values
    $categories                   = get_option( 'direktt_loyalty_program_categories', 0 );
    $tags                         = get_option( 'direktt_loyalty_program_tags', 0 );
    $initial_points               = intval( get_option( 'direktt_loyalty_program_initial_points', 0 ) );
    $points_rules                 = get_option( 'direktt_loyalty_points_rules', array() );
    $loyalty_user                 = get_option( 'direktt_loyalty_user', 'no' ) === 'yes';
    $loyalty_user_template        = intval( get_option( 'direktt_loyalty_user_template', 0 ) );
    $loyalty_admin                = get_option( 'direktt_loyalty_admin', 'no' ) === 'yes';
    $loyalty_admin_template       = intval( get_option( 'direktt_loyalty_admin_template', 0 ) );
    $loyalty_user_reset           = get_option( 'direktt_loyalty_user_reset', 'no' ) === 'yes';
    $loyalty_user_template_reset  = intval( get_option( 'direktt_loyalty_user_template_reset', 0 ) );
    $loyalty_admin_reset          = get_option( 'direktt_loyalty_admin_reset', 'no' ) === 'yes';
    $loyalty_admin_template_reset = intval( get_option( 'direktt_loyalty_admin_template_reset', 0 ) );

    // Query for template posts
    $template_args  = array(
        'post_type'      => 'direkttmtemplates',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => array(
            array(
                'key'     => 'direkttMTType',
                'value'   => array( 'all', 'none' ),
                'compare' => 'IN',
            ),
        ),
    );
    $template_posts = get_posts( $template_args );

    $all_categories = Direktt_User::get_all_user_categories();
    $all_tags       = Direktt_User::get_all_user_tags();
    ?>
    <div class="wrap">
        <?php if ( $success ) : ?>
            <div class="updated notice is-dismissible">
                <p><?php echo esc_html__( 'Settings saved successfully.', 'direktt-loyalty-program' ); ?></p>
            </div>
        <?php endif; ?>
        <form method="post" action="">
            <?php wp_nonce_field( 'direktt_admin_loyalty_program_save', 'direktt_admin_loyalty_program_nonce' ); ?>

            <table class="form-table">
                <tr>
                    <th scope="row"><label for="direktt_loyalty_program_categories"><?php echo esc_html__( 'Category', 'direktt-loyalty-program' ); ?></label></th>
                    <td>
                        <select name="direktt_loyalty_program_categories" id="direktt_loyalty_program_categories">
                            <option value="0"><?php echo esc_html__( 'Select Category', 'direktt-loyalty-program' ); ?></option>
                            <?php foreach ( $all_categories as $category ) : ?>
                                <option value="<?php echo esc_attr( $category['value'] ); ?>" <?php selected( $categories, $category['value'] ); ?>>
                                    <?php echo esc_html( $category['name'] ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php echo esc_html__( 'Users with this category will be admin of Loyalty Program.', 'direktt-loyalty-program' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="direktt_loyalty_program_tags"><?php echo esc_html__( 'Tag', 'direktt-loyalty-program' ); ?></label></th>
                    <td>
                        <select name="direktt_loyalty_program_tags" id="direktt_loyalty_program_tags">
                            <option value="0"><?php echo esc_html__( 'Select Tag', 'direktt-loyalty-program' ); ?></option>
                            <?php foreach ( $all_tags as $tag ) : ?>
                                <option value="<?php echo esc_attr( $tag['value'] ); ?>" <?php selected( $tags, $tag['value'] ); ?>>
                                    <?php echo esc_html( $tag['name'] ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php echo esc_html__( 'Users with this tag will be admin of Loyalty Program.', 'direktt-loyalty-program' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="direktt_loyalty_program_initial_points"><?php echo esc_html__( 'Initial Points', 'direktt-loyalty-program' ); ?></label></th>
                    <td>
                        <input type="number" name="direktt_loyalty_program_initial_points" id="direktt_loyalty_program_initial_points" value="<?php echo esc_attr( $initial_points ); ?>" min="0" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html__( 'Points Rules', 'direktt-loyalty-program' ); ?></th>
                    <td>
                        <div id="direktt_points_repeater">
                            <!-- Existing points rules will be rendered here -->
                        </div>
                        <button type="button" class="button" id="add_points_rule"><?php echo esc_html__( 'Add Points Rule', 'direktt-loyalty-program' ); ?></button>
                        <script>
                            (function($) {
                        
                                var pointsRules = <?php echo wp_json_encode( get_option( 'direktt_loyalty_points_rules', array() ) ); ?>;

                                function renderRule(index, value) {
                                    return `
                                <div class="direktt-loyalty-program-points-rule" style="margin-bottom:8px;">
                                    <label>
                                        <input type="number" name="direktt_loyalty_points_rules[]" value="${value ? value : 1}" placeholder="Points" min="1" />
                                    </label>
                                    <button type="button" class="button direktt_loyalty_program_remove_points_rule"><?php echo esc_js( __( 'Remove', 'direktt-loyalty-program' ) ); ?></button>
                                </div>`;
                                }

                                function refreshRules() {
                                    var html = '';
                                    if (pointsRules.length) {
                                        for (var i = 0; i < pointsRules.length; i++) {
                                            html += renderRule(i, pointsRules[i]);
                                        }
                                    }
                                    $('#direktt_points_repeater').html(html);
                                }
                                $(document).ready(function() {
                                    refreshRules();
                                    $('#add_points_rule').off('click').on('click', function(e) {
                                        e.preventDefault();
                                        $('#direktt_points_repeater').append(renderRule('', ''));
                                    });
                                    $('#direktt_points_repeater').off('click').on('click', '.direktt_loyalty_program_remove_points_rule', function(e) {
                                        e.preventDefault();
                                        $(this).closest('.direktt-loyalty-program-points-rule').remove();
                                    });
                                });
                            })(jQuery);
                        </script>
                        <p class="description"><?php echo esc_html__( 'Add rules for awarding points (e.g. 10).', 'direktt-loyalty-program' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="direktt_loyalty_user"><?php echo esc_html__( 'Send to Subscriber', 'direktt-loyalty-program' ); ?></label></th>
                    <td>
                        <input type="checkbox" name="direktt_loyalty_user" id="direktt_loyalty_user" value="yes" <?php checked( $loyalty_user ); ?> />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="direktt_loyalty_user_template"><?php echo esc_html__( 'Subscriber Message Template', 'direktt-loyalty-program' ); ?></label></th>
                    <td>
                        <select name="direktt_loyalty_user_template" id="direktt_loyalty_user_template">
                            <option value="0"><?php echo esc_html__( 'Select Template', 'direktt-loyalty-program' ); ?></option>
                            <?php foreach ( $template_posts as $post ) : ?>
                                <option value="<?php echo esc_attr( $post->ID ); ?>" <?php selected( $loyalty_user_template, $post->ID ); ?>>
                                    <?php echo esc_html( $post->post_title ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php echo esc_html__( 'In message template you can use', 'direktt-loyalty-program' ); ?> <?php echo esc_html( '#change#' ); ?> <?php echo esc_html__( 'which will be replaced with number of points changed.', 'direktt-loyalty-program' ); ?></p>
                        <p class="description"><?php echo esc_html__( 'And you can use', 'direktt-loyalty-program' ); ?> <?php echo esc_html( '#points#' ); ?> <?php echo esc_html__( 'for new points balance.', 'direktt-loyalty-program' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="direktt_loyalty_admin"><?php echo esc_html__( 'Send to Admin', 'direktt-loyalty-program' ); ?></label></th>
                    <td>
                        <input type="checkbox" name="direktt_loyalty_admin" id="direktt_loyalty_admin" value="yes" <?php checked( $loyalty_admin ); ?> />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="direktt_loyalty_admin_template"><?php echo esc_html__( 'Admin Message Template', 'direktt-loyalty-program' ); ?></label></th>
                    <td>
                        <select name="direktt_loyalty_admin_template" id="direktt_loyalty_admin_template">
                            <option value="0"><?php echo esc_html__( 'Select Template', 'direktt-loyalty-program' ); ?></option>
                            <?php foreach ( $template_posts as $post ) : ?>
                                <option value="<?php echo esc_attr( $post->ID ); ?>" <?php selected( $loyalty_admin_template, $post->ID ); ?>>
                                    <?php echo esc_html( $post->post_title ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php echo esc_html__( 'In message template you can use', 'direktt-loyalty-program' ); ?> <?php echo esc_html( '#change#' ); ?> <?php echo esc_html__( 'which will be replaced with number of points changed.', 'direktt-loyalty-program' ); ?></p>
                        <p class="description"><?php echo esc_html( '#points#' ); ?> <?php echo esc_html__( 'for new points balance,', 'direktt-loyalty-program' ); ?> <?php echo esc_html( '#display_name#' ); ?> <?php echo esc_html__( 'for display name, and', 'direktt-loyalty-program' ); ?> <?php echo esc_html( '#subscription_id#' ); ?> <?php echo esc_html__( 'for subscription id.', 'direktt-loyalty-program' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="direktt_loyalty_user_reset"><?php echo esc_html__( 'Send reset message to Subscriber', 'direktt-loyalty-program' ); ?></label></th>
                    <td>
                        <input type="checkbox" name="direktt_loyalty_user_reset" id="direktt_loyalty_user_reset" value="yes" <?php checked( $loyalty_user_reset ); ?> />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="direktt_loyalty_user_template_reset"><?php echo esc_html__( 'Subscriber Reset Message Template', 'direktt-loyalty-program' ); ?></label></th>
                    <td>
                        <select name="direktt_loyalty_user_template_reset" id="direktt_loyalty_user_template_reset">
                            <option value="0"><?php echo esc_html__( 'Select Template', 'direktt-loyalty-program' ); ?></option>
                            <?php foreach ( $template_posts as $post ) : ?>
                                <option value="<?php echo esc_attr( $post->ID ); ?>" <?php selected( $loyalty_user_template_reset, $post->ID ); ?>>
                                    <?php echo esc_html( $post->post_title ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php echo esc_html__( 'In message template you can use', 'direktt-loyalty-program' ); ?> <?php echo esc_html( '#points#' ); ?> <?php echo esc_html__( 'for new points balance.', 'direktt-loyalty-program' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="direktt_loyalty_admin_reset"><?php echo esc_html__( 'Send reset message to Admin', 'direktt-loyalty-program' ); ?></label></th>
                    <td>
                        <input type="checkbox" name="direktt_loyalty_admin_reset" id="direktt_loyalty_admin_reset" value="yes" <?php checked( $loyalty_admin_reset ); ?> />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="direktt_loyalty_admin_template_reset"><?php echo esc_html__( 'Admin Reset Message Template', 'direktt-loyalty-program' ); ?></label></th>
                    <td>
                        <select name="direktt_loyalty_admin_template_reset" id="direktt_loyalty_admin_template_reset">
                            <option value="0"><?php echo esc_html__( 'Select Template', 'direktt-loyalty-program' ); ?></option>
                            <?php foreach ( $template_posts as $post ) : ?>
                                <option value="<?php echo esc_attr( $post->ID ); ?>" <?php selected( $loyalty_admin_template_reset, $post->ID ); ?>>
                                    <?php echo esc_html( $post->post_title ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php echo esc_html__( 'In message template you can use', 'direktt-loyalty-program' ); ?> <?php echo esc_html( '#points#' ); ?> <?php echo esc_html__( 'for new points balance,', 'direktt-loyalty-program' ); ?> <?php echo esc_html( '#display_name#' ); ?> <?php echo esc_html__( 'for display name, and', 'direktt-loyalty-program' ); ?> <?php echo esc_html( '#subscription_id#' ); ?> <?php echo esc_html__( 'for subscription id.', 'direktt-loyalty-program' ); ?></p>
                    </td>
                </tr>
            </table>

            <?php submit_button( esc_html__( 'Save Settings', 'direktt-loyalty-program' ) ); ?>
        </form>
    </div>
	<?php
}

add_action( 'direktt_setup_profile_tools', 'setup_loyalty_program_profile_tools' );

function setup_loyalty_program_profile_tools() {
    $selected_category = intval( get_option( 'direktt_loyalty_program_categories', 0 ) );
    $selected_tag      = intval( get_option( 'direktt_loyalty_program_tags', 0 ) );

    if ( $selected_category !== 0 ) {
        $category      = get_term( $selected_category, 'direkttusercategories' );
        $category_slug = $category ? $category->slug : '';
    } else {
        $category_slug = '';
    }

    if ( $selected_tag !== 0 ) {
        $tag      = get_term( $selected_tag, 'direkttusertags' );
        $tag_slug = $tag ? $tag->slug : '';
    } else {
        $tag_slug = '';
    }

    Direktt_Profile::add_profile_tool(
        array(
            'id'         => 'loyalty-program-tool',
            'label'      => __( 'Loyalty Program', 'direktt-loyalty-program' ),
            'callback'   => 'render_loyalty_program_tool',
            'categories' => $category_slug ? array( $category_slug ) : array(),
            'tags'       => $tag_slug ? array( $tag_slug ) : array(),
            'priority'   => 2,
        )
    );
}

function render_loyalty_program_tool() {
    $subscription_id = isset( $_GET['subscriptionId'] ) ? sanitize_text_field( wp_unslash( $_GET['subscriptionId'] ) ) : false;
    $profile_user    = Direktt_User::get_user_by_subscription_id( $subscription_id );
    if ( ! $profile_user ) {
        echo '<div class="notice notice-error"><p>' . esc_html__( 'User not found.', 'direktt-loyalty-program' ) . '</p></div>';
        return;
    }
    $user_id        = $profile_user['ID'];
    $initial_points = intval( get_option( 'direktt_loyalty_program_initial_points', 0 ) );
    if ( $initial_points > 0 && ! get_post_meta( $user_id, 'direktt_loyalty_points', true ) ) {
        update_post_meta( $user_id, 'direktt_loyalty_points', $initial_points );
    }
    $user_points            = intval( get_post_meta( $user_id, 'direktt_loyalty_points', true ) );
    $points_rules           = get_option( 'direktt_loyalty_points_rules', array() );
    $loyalty_user           = get_option( 'direktt_loyalty_user', 'no' ) === 'yes';
    $loyalty_user_template  = intval( get_option( 'direktt_loyalty_user_template', 0 ) );
    $loyalty_admin          = get_option( 'direktt_loyalty_admin', 'no' ) === 'yes';
    $loyalty_admin_template = intval( get_option( 'direktt_loyalty_admin_template', 0 ) );

    $loyalty_user_reset           = get_option( 'direktt_loyalty_user_reset', 'no' ) === 'yes';
    $loyalty_user_template_reset  = intval( get_option( 'direktt_loyalty_user_template_reset', 0 ) );
    $loyalty_admin_reset          = get_option( 'direktt_loyalty_admin_reset', 'no' ) === 'yes';
    $loyalty_admin_template_reset = intval( get_option( 'direktt_loyalty_admin_template_reset', 0 ) );

    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['direktt_loyalty_points_nonce'] ) && wp_verify_nonce( $_POST['direktt_loyalty_points_nonce'], 'direktt_loyalty_points_action' ) ) {
        if ( isset( $_POST['points_change'] ) && $_POST['points_change'] !== '' ) {
            $change       = intval( $_POST['points_change'] );
            $user_points += $change;
            update_post_meta( $user_id, 'direktt_loyalty_points', $user_points );
            global $direktt_user;
            $admin_id     = $direktt_user['ID'];
            $transaction  = array(
                'admin_id'    => $admin_id,
                'timestamp'   => time(),
                'change'      => $change,
                'new_balance' => $user_points,
            );
            $transactions = get_post_meta( $user_id, 'direktt_loyalty_transactions', true );
            if ( ! is_array( $transactions ) ) {
                $transactions = array();
            }
            $transactions[] = $transaction;
            update_post_meta( $user_id, 'direktt_loyalty_transactions', $transactions );

            if ( $loyalty_user && $loyalty_user_template !== 0 ) {
                Direktt_Message::send_message_template(
                    array( $subscription_id ),
                    $loyalty_user_template,
                    array(
                        'change' => $change,
                        'points' => $user_points,
                    )
                );
            }

            if ( $loyalty_admin && $loyalty_admin_template !== 0 ) {
                Direktt_Message::send_message_template_to_admin(
                    $loyalty_admin_template,
                    array(
                        'change'          => $change,
                        'points'          => $user_points,
                        'display_name'    => get_the_title( $profile_user['ID'] ) ?? '-',
                        'subscription_id' => $subscription_id ?? '-',
                    )
                );
            }

            set_transient( 'direktt_loyalty_success_message', __( 'Points updated successfully. New balance: ', 'direktt-loyalty-program' ) . $user_points, 30 ); // Message lasts for 30 seconds
            wp_safe_redirect( $_SERVER['REQUEST_URI'] );
            exit;
        }

        if ( isset( $_POST['reset_points'] ) && $_POST['reset_points'] === '1' ) {
            update_post_meta( $user_id, 'direktt_loyalty_points', $initial_points );
            global $direktt_user;
            $admin_id    = $direktt_user['ID'];
            $transaction = array(
                'admin_id'    => $admin_id,
                'timestamp'   => time(),
                'change'      => 'reset',
                'new_balance' => $initial_points,
            );

            $transactions = get_post_meta( $user_id, 'direktt_loyalty_transactions', true );
            if ( ! is_array( $transactions ) ) {
                $transactions = array();
            }
            $transactions[] = $transaction;
            update_post_meta( $user_id, 'direktt_loyalty_transactions', $transactions );

            if ( $loyalty_user_reset && $loyalty_user_template_reset !== 0 ) {
                Direktt_Message::send_message_template(
                    array( $subscription_id ),
                    $loyalty_user_template_reset,
                    array(
                        'points' => $initial_points,
                    )
                );
            }

            if ( $loyalty_admin_reset && $loyalty_admin_template_reset !== 0 ) {
                Direktt_Message::send_message_template_to_admin(
                    $loyalty_admin_template_reset,
                    array(
                        'points'          => $initial_points,
                        'display_name'    => get_the_title( $profile_user['ID'] ) ?? '-',
                        'subscription_id' => $subscription_id ?? '-',
                    )
                );
            }

            set_transient( 'direktt_loyalty_success_message', __( 'Points reset successfully. New balance: ', 'direktt-loyalty-program' ) . $initial_points, 30 ); // Message lasts for 30 seconds
            wp_safe_redirect( $_SERVER['REQUEST_URI'] );
            exit;
        }
    }

    // Check if a success message is set and display it
    if ( $message = get_transient( 'direktt_loyalty_success_message' ) ) {
        echo '<div class="updated notice is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
        delete_transient( 'direktt_loyalty_success_message' ); // Clear the message after it's shown
    }
	?>
    <script>
        jQuery(document).ready(function($) {
            // Show confirmation popup
            $('button[name="points_change_btn"]').off('click').on('click', function(e) {
                e.preventDefault();
                var changeValue = $(this).val();
                $('#direktt-loyalty-program-confirm').addClass('direktt-popup-on');
                $('#direktt-loyalty-program-confirm .direktt-popup-yes').data('change-value', changeValue);
                if (changeValue < 0) {
                    $('#direktt-loyalty-program-confirm .direktt-popup-text').text($('#direktt-loyalty-program-confirm .direktt-popup-text').text().replace('__POINTS__', '<?php echo esc_js( __( 'remove', 'direktt-loyalty-program' ) ); ?> ' + Math.abs(changeValue)));
                } else {
                    $('#direktt-loyalty-program-confirm .direktt-popup-text').text($('#direktt-loyalty-program-confirm .direktt-popup-text').text().replace('__POINTS__', '<?php echo esc_js( __( 'add', 'direktt-loyalty-program' ) ); ?> ' + changeValue));
                }
            });

            $('#direktt-loyalty-program-confirm .direktt-popup-no').off('click').on('click', function() {
                $('#direktt-loyalty-program-confirm').removeClass('direktt-popup-on');
                setTimeout(function() {
                    // Reset the confirmation text
                    $('#direktt-loyalty-program-confirm .direktt-popup-text').text('<?php echo esc_js( __( 'Are you sure that you want to', 'direktt-loyalty-program' ) ); ?> __POINTS__ <?php echo esc_js( __( 'points.', 'direktt-loyalty-program' ) ); ?>');
                }, 300);
            });

            $('#direktt-loyalty-program-confirm .direktt-popup-yes').off('click').on('click', function() {
                var changeValue = $(this).data('change-value');
                $('#direktt-loyalty-program-confirm').removeClass('direktt-popup-on');
                $('.direktt-loader-overlay').fadeIn();
                // Submit the form with the change value
                $('<input>').attr({
                    type: 'hidden',
                    name: 'points_change',
                    value: changeValue
                }).appendTo('form');
                setTimeout(function() {
                    // $('#direktt-loader-overlay').fadeOut();
                    $('form').submit();
                }, 500);
            });

            $('#reset_points_btn').off('click').on('click', function(e) {
                e.preventDefault();
                $('#direktt-loyalty-program-reset').addClass( 'direktt-popup-on' );
            });

            $('#direktt-loyalty-program-reset .direktt-popup-no').off('click').on('click', function() {
                $('#direktt-loyalty-program-reset').removeClass('direktt-popup-on');
            });

            $('#direktt-loyalty-program-reset .direktt-popup-yes').off('click').on('click', function() {
                $('#direktt-loyalty-program-reset').removeClass('direktt-popup-on');
                $('.direktt-loader-overlay').fadeIn();
                // Submit the form to reset points
                $('<input>').attr({
                    type: 'hidden',
                    name: 'reset_points',
                    value: '1'
                }).appendTo('form');
                setTimeout(function() {
                    // $('#direktt-loader-overlay').fadeOut();
                    $('form').submit();
                }, 500);
            });
        });
    </script>
    <?php
    echo Direktt_Public::direktt_render_confirm_popup( 'direktt-loyalty-program-confirm', __( 'Are you sure that you want to', 'direktt-loyalty-program' ) . ' __POINTS__ ' . __( 'points.', 'direktt-loyalty-program' ) );
    echo Direktt_Public::direktt_render_confirm_popup( 'direktt-loyalty-program-reset', __( 'Are you sure that you want to reset the points.', 'direktt-loyalty-program' ) );
    echo Direktt_Public::direktt_render_alert_popup( '', __( 'Placeholder text, Lorem Ipsum', 'direktt-loyalty-program' ) );
    echo Direktt_Public::direktt_render_loader( __( 'Don\'t refresh the page', 'direktt-loyalty-program' ) );
    ?>
    <div class="direktt-loyalty-program-wrap">
        <h2><?php echo esc_html__( 'Loyalty Program', 'direktt-loyalty-program' ); ?></h2>
        <p><?php echo esc_html__( 'Points: ', 'direktt-loyalty-program' ); ?> <strong><?php echo esc_html( $user_points ); ?></strong></p>
        <form method="post">
            <?php
            wp_nonce_field( 'direktt_loyalty_points_action', 'direktt_loyalty_points_nonce' );
            // Filter and sort rules descending for minus buttons
            $rules = array_filter(
                array_map( 'intval', $points_rules ),
                function ( $v ) {
					return $v > 0;
				}
            );
            if ( ! empty( $rules ) ) {
				?>
                <div class="direktt-loyalty-program-rules">
                    <?php
                    rsort( $rules, SORT_NUMERIC );
                    foreach ( $rules as $rule ) :
						?>
                        <button name="points_change_btn" value="-<?php echo esc_attr( $rule ); ?>" class="button-red"><?php echo esc_html( '-' ) . esc_html( $rule ); ?></button>
                    <?php endforeach; ?>

                    <?php
                    // Sort rules ascending for plus buttons
                    $rules_asc = $rules;
                    sort( $rules_asc, SORT_NUMERIC );
                    foreach ( $rules_asc as $rule ) :
						?>
                        <button name="points_change_btn" value="<?php echo esc_attr( $rule ); ?>" class="button-green"><?php echo esc_html( '+' ) . esc_html( $rule ); ?></button>
                    <?php endforeach; ?>
                </div>
            <?php } ?>
            <button name="reset_points_btn" id="reset_points_btn" class="button-green"><?php echo esc_html__( 'Reset points', 'direktt-loyalty-program' ); ?></button>
        </form>
        <?php
        $transactions = get_post_meta( $user_id, 'direktt_loyalty_transactions', true );
        if ( ! is_array( $transactions ) ) {
            $transactions = array();
        }

        if ( empty( $transactions ) ) {
            echo '<div class="direktt-loyalty-program-transactions">';
            echo '<h3>' . esc_html__( 'Recent Transactions', 'direktt-loyalty-program' ) . '</h3>';
            echo '<p>' . esc_html__( 'No transactions found.', 'direktt-loyalty-program' ) . '</p>';
            echo '</div>';
        } else {
            $transactions = array_reverse( $transactions );
            $transactions = array_slice( $transactions, 0, 20 );
            echo '<div class="direktt-loyalty-program-transactions">';
            echo '<h3>' . esc_html__( 'Recent Transactions', 'direktt-loyalty-program' ) . '</h3>';
            echo '<table>';
            echo '<thead>';
				echo '<tr>';
					echo '<th>' . esc_html__( 'Points', 'direktt-loyalty-program' ) . '</th>';
					echo '<th>' . esc_html__( 'Time', 'direktt-loyalty-program' ) . '</th>';
			if ( Direktt_User::is_direktt_admin() ) {
				echo '<th>' . esc_html__( 'User', 'direktt-loyalty-program' ) . '</th>';
			}
					echo '<th>' . esc_html__( 'Balance', 'direktt-loyalty-program' ) . '</th>';
				echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ( $transactions as $transaction ) {
                $date         = human_time_diff( $transaction['timestamp'] ) . ' ago';
                $change       = $transaction['change'];
                $new_balance  = $transaction['new_balance'];
                $admin_id     = $transaction['admin_id'];
                $direktt_user = Direktt_User::get_user_by_post_id( $admin_id );
                if ( $direktt_user ) {
                    $display_name = get_the_title( $direktt_user['ID'] );
                } else {
                    $display_name = esc_html__( 'Unknown', 'direktt-loyalty-program' );
                }
                echo '<tr>';
                echo '<td>';
                if ( $change === 'reset' ) {
                    esc_html_e( 'Reset', 'direktt-loyalty-program' );
                } else {
                    echo ( '<strong>' . ( $change > 0 ? esc_html( '+' ) : '' ) . esc_html( $change ) . '</strong>' );
                }
                echo '</td>';
                echo '<td>' . esc_html( $date ) . '</td>';
                if ( Direktt_User::is_direktt_admin() ) {
                    echo '<td>' . esc_html( $display_name ) . '</td>';
                }
                echo '<td>' . esc_html( $new_balance ) . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        }
        ?>
    </div>
	<?php
}

function direktt_loyalty_add_meta_box() {
    add_meta_box(
        'direktt_loyalty_program_meta_box',
        esc_html__( 'Loyalty Program', 'direktt-loyalty-program' ),
        'render_loyalty_program_meta_box',
        'direkttusers',
        'advanced',
        'default'
    );
}

add_action( 'add_meta_boxes', 'direktt_loyalty_add_meta_box' );

function render_loyalty_program_meta_box( $post ) {
    $user_id        = $post->ID;
    $user_points    = intval( get_post_meta( $user_id, 'direktt_loyalty_points', true ) );
    $initial_points = intval( get_option( 'direktt_loyalty_program_initial_points', 0 ) );

    if ( $initial_points > 0 && ! $user_points ) {
        update_post_meta( $user_id, 'direktt_loyalty_points', $initial_points );
        $user_points = $initial_points;
    }

	?>
    <div class="direktt-loyalty-program-meta-box">
        <div class="direktt-loyalty-program-points">
            <p><?php echo esc_html__( 'Current Points: ', 'direktt-loyalty-program' ) . '<strong>' . esc_html( $user_points ) . '</strong>'; ?></p>
        </div>
        <div class="direktt-loyalty-program-transactions">
            <h3><?php echo esc_html__( 'Recent Transactions', 'direktt-loyalty-program' ); ?></h3>
            <?php
            $transactions = get_post_meta( $user_id, 'direktt_loyalty_transactions', true );
            if ( is_array( $transactions ) && ! empty( $transactions ) ) {
                $transactions = array_reverse( $transactions );
                $transactions = array_slice( $transactions, 0, 20 );
                echo '<table class="widefat striped">';
					echo '<thead>';
							echo '<tr>';
							echo '<th>' . esc_html__( 'Points', 'direktt-loyalty-program' ) . '</th>';
							echo '<th>' . esc_html__( 'Time', 'direktt-loyalty-program' ) . '</th>';
							echo '<th>' . esc_html__( 'User', 'direktt-loyalty-program' ) . '</th>';
							echo '<th>' . esc_html__( 'Balance', 'direktt-loyalty-program' ) . '</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
				foreach ( $transactions as $transaction ) {
					$admin_id     = $transaction['admin_id'];
					$direktt_user = Direktt_User::get_user_by_post_id( $admin_id );
					if ( $direktt_user ) {
						$display_name = get_the_title( $direktt_user['ID'] );
					} else {
						$display_name = esc_html__( 'Unknown', 'direktt-loyalty-program' );
					}
					$date        = wp_date( 'Y-m-d H:i:s', $transaction['timestamp'] );
					$change      = $transaction['change'];
					$new_balance = $transaction['new_balance'];

					echo '<tr>';
					echo '<td class="direktt-loyalty-program-points">';
					if ( $change === 'reset' ) {
						esc_html_e( 'Reset', 'direktt-loyalty-program' );
					} else {
						echo ( '<strong>' . ( $change > 0 ? esc_html( '+' ) : '' ) . esc_html( $change ) . '</strong>' );
					}
					echo '</td>';
					echo '<td>' . esc_html( $date ) . '</td>';
					echo '<td>' . esc_html( $display_name ) . '</td>';
					echo '<td>' . esc_html( $new_balance ) . '</td>';
					echo '</tr>';
				}
					echo '</tbody>';
                echo '</table>';
            } else {
                echo '<p>' . esc_html__( 'No transactions found.', 'direktt-loyalty-program' ) . '</p>';
            }
            ?>
        </div>
    </div>
	<?php
}

function loyalty_program_service_shortcode() {
    ob_start();
    echo '<div id="direktt-profile-wrap">';
    $user         = wp_get_current_user();
    $direktt_user = Direktt_User::get_direktt_user_by_wp_user( $user );
    if ( ! $direktt_user ) {
        echo '<div class="notice notice-error"><p>' . esc_html__( 'You must be logged in to view the loyalty program.', 'direktt-loyalty-program' ) . '</p></div>';
        echo '</div>';
        return ob_get_clean();
    }

    $points       = intval( get_post_meta( $direktt_user['ID'], 'direktt_loyalty_points', true ) );
    $transactions = get_post_meta( $direktt_user['ID'], 'direktt_loyalty_transactions', true );
    if ( ! is_array( $transactions ) ) {
        $transactions = array();
        echo '<div class="direktt-loyalty-program-service">';
        echo '<div class="direktt-loyalty-program-points">';
        echo '<p>' . esc_html__( 'Current Points: ', 'direktt-loyalty-program' ) . '<strong>' . esc_html( $points ) . '</strong></p>';
        echo '</div>';
        echo '<div class="direktt-loyalty-program-transactions">';
        echo '<h3>' . esc_html__( 'Recent Transactions', 'direktt-loyalty-program' ) . '</h3>';
        echo '<p>' . esc_html__( 'No transactions found.', 'direktt-loyalty-program' ) . '</p>';
        echo '</div>';
        echo '</div>';
    } else {
        $transactions = array_reverse( $transactions );
        $transactions = array_slice( $transactions, 0, 20 );

        echo '<div class="direktt-loyalty-program-service">';
        echo '<div class="direktt-loyalty-program-points">';
        echo '<p>' . esc_html__( 'Current Points: ', 'direktt-loyalty-program' ) . '<strong>' . esc_html( $points ) . '</strong></p>';
        echo '</div>';
        echo '<div class="direktt-loyalty-program-transactions">';
        echo '<h3>' . esc_html__( 'Recent Transactions', 'direktt-loyalty-program' ) . '</h3>';
        echo '<ul>';

        echo '<table>';
        echo '<tr>';
        echo '<th>' . esc_html__( 'Points', 'direktt-loyalty-program' ) . '</th>';
        echo '<th>' . esc_html__( 'Time', 'direktt-loyalty-program' ) . '</th>';
        echo '<th>' . esc_html__( 'Balance', 'direktt-loyalty-program' ) . '</th>';
        echo '</tr>';
        foreach ( $transactions as $transaction ) {
            $date        = wp_date( 'Y-m-d H:i:s', $transaction['timestamp'] );
            $change      = $transaction['change'];
            $new_balance = $transaction['new_balance'];
            echo '<tr>';
            echo '<td>';
            if ( $change === 'reset' ) {
                esc_html_e( 'Reset', 'direktt-loyalty-program' );
            } else {
                echo ( '<strong>' . ( $change > 0 ? esc_html( '+' ) : '' ) . esc_html( $change ) . '</strong>' );
            }
            echo '</td>';
            echo '<td>' . esc_html( $date ) . '</td>';
            echo '<td>' . esc_html( $new_balance ) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';
        echo '</div>';
    }

    echo '</div>';
    return ob_get_clean();
}

add_shortcode( 'direktt_loyalty_program_service', 'loyalty_program_service_shortcode' );
