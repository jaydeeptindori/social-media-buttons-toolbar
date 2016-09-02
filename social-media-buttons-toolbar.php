<?php
/**
 * Plugin Name: Social Media Buttons Toolbar
 * Plugin URI: https://github.com/ArthurGareginyan/social-media-buttons-toolbar
 * Description: Easily add the smart toolbar with social media buttons (not share, only link to your profiles) to any place of your WordPress website.
 * Author: Arthur Gareginyan
 * Author URI: http://www.arthurgareginyan.com
 * Version: 3.1
 * License: GPL3
 * Text Domain: social-media-buttons-toolbar
 * Domain Path: /languages/
 *
 * Copyright 2015-2016 Arthur Gareginyan (email : arthurgareginyan@gmail.com)
 *
 * This file is part of "Social Media Buttons Toolbar".
 *
 * "Social Media Buttons Toolbar" is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * "Social Media Buttons Toolbar" is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with "Social Media Buttons Toolbar".  If not, see <http://www.gnu.org/licenses/>.
 *
 */


/**
 * Prevent Direct Access
 *
 * @since 0.1
 */
defined('ABSPATH') or die("Restricted access!");

/**
 * Define global constants
 *
 * @since 3.1
 */
defined('SMEDIABT_DIR') or define('SMEDIABT_DIR', dirname(plugin_basename(__FILE__)));
defined('SMEDIABT_BASE') or define('SMEDIABT_BASE', plugin_basename(__FILE__));
defined('SMEDIABT_URL') or define('SMEDIABT_URL', plugin_dir_url(__FILE__));
defined('SMEDIABT_PATH') or define('SMEDIABT_PATH', plugin_dir_path(__FILE__));
defined('SMEDIABT_VERSION') or define('SMEDIABT_VERSION', '3.1');

/**
 * Register text domain
 *
 * @since 2.0
 */
function smbtoolbar_textdomain() {
	load_plugin_textdomain( 'social-media-buttons-toolbar', false, SMEDIABT_DIR . '/languages/' );
}
add_action( 'init', 'smbtoolbar_textdomain' );

/**
 * Print direct link to Social Media Buttons Toolbar admin page
 *
 * Fetches array of links generated by WP Plugin admin page ( Deactivate | Edit )
 * and inserts a link to the Social Media Buttons Toolbar admin page
 *
 * @since  2.0
 * @param  array $links Array of links generated by WP in Plugin Admin page.
 * @return array        Array of links to be output on Plugin Admin page.
 */
function smbtoolbar_settings_link( $links ) {
	$settings_page = '<a href="' . admin_url( 'options-general.php?page=social-media-buttons-toolbar.php' ) .'">' . __( 'Settings', 'social-media-buttons-toolbar' ) . '</a>';
	array_unshift( $links, $settings_page );
	return $links;
}
add_filter( "plugin_action_links_".SMEDIABT_BASE, 'smbtoolbar_settings_link' );

/**
 * Register "Social Media Buttons Toolbar" submenu in "Settings" Admin Menu
 *
 * @since 2.0
 */
function smbtoolbar_register_submenu_page() {
	add_options_page( __( 'Social Media Buttons Toolbar', 'social-media-buttons-toolbar' ), __( 'Social Buttons', 'social-media-buttons-toolbar' ), 'manage_options', basename( __FILE__ ), 'smbtoolbar_render_submenu_page' );
}
add_action( 'admin_menu', 'smbtoolbar_register_submenu_page' );

/**
 * Attach Settings Page
 *
 * @since 3.0
 */
require_once( SMEDIABT_PATH . 'inc/php/settings_page.php' );

/**
 * Load scripts and style sheet for settings page
 *
 * @since 3.1
 */
function smbtoolbar_load_scripts($hook) {

    // Return if the page is not a settings page of this plugin
    if ( 'settings_page_social-media-buttons-toolbar' != $hook ) {
        return;
    }

    // Style sheet
    wp_enqueue_style( 'smbtoolbar-admin-css', SMEDIABT_URL . 'inc/css/admin.css' );
    wp_enqueue_style( 'smbtoolbar-bootstrap', SMEDIABT_URL . 'inc/css/bootstrap.css' );
    wp_enqueue_style( 'smbtoolbar-bootstrap-theme', SMEDIABT_URL . 'inc/css/bootstrap-theme.css' );

    // JavaScript
    wp_enqueue_script( 'smbtoolbar-admin-js', SMEDIABT_URL . 'inc/js/admin.js', array(), false, true );
    wp_enqueue_script( 'smbtoolbar-bootstrap-checkbox', SMEDIABT_URL . 'inc/js/bootstrap-checkbox.min.js' );

}
add_action( 'admin_enqueue_scripts', 'smbtoolbar_load_scripts' );

/**
 * Register settings
 *
 * @since 0.1
 */
function smbtoolbar_register_settings() {
	register_setting( 'smbtoolbar_settings_group', 'smbtoolbar_settings' );
}
add_action( 'admin_init', 'smbtoolbar_register_settings' );

/**
 * Render fields for saving social media data to BD
 *
 * @since 1.4
 */
function smbtoolbar_media($name, $label, $placeholder, $help=null, $link=null) {

    // Declare variables
    $options = get_option( 'smbtoolbar_settings' );

    if ( !empty($options["media"][$name]["content"]) ) :
        $value = esc_textarea( $options["media"][$name]["content"] );
    else :
        $value = "";
    endif;

    // Generate the table
    if ( !empty($link) ) :
        $link_out = "<a href='$link' target='_blank'>$label</a>";
    else :
        $link_out = "$label";
    endif;

    $label = "<input type='hidden' name='smbtoolbar_settings[media][$name][label]' value='$label'>";
    $slug = "<input type='hidden' name='smbtoolbar_settings[media][$name][slug]' value='$name'>";
    $field_out = "<input type='text' name='smbtoolbar_settings[media][$name][content]' size='50' value='$value' placeholder='$placeholder'>";

    // Put table to the variables $out and $help_out
    $out = "<tr valign='top'>
                <th scope='row'>
                    $link_out
                </th>
                <td>
                    $label
                    $slug
                    $field_out
                </td>
            </tr>";
    if ( !empty($help) ) :
        $help_out = "<tr valign='top'>
                        <td></td>
                        <td class='help-text'>
                            $help
                        </td>
                     </tr>";
    else :
        $help_out = "";
    endif;

    // Print the generated table
    echo $out . $help_out;
}

/**
 * Render checkboxes and fields for saving settings data to BD
 *
 * @since 1.0
 */
function smbtoolbar_setting($name, $label, $help=null, $field=null, $placeholder=null, $size=null) {

    // Declare variables
    $options = get_option( 'smbtoolbar_settings' );

    if ( !empty($options[$name]) ) :
        $value = esc_textarea( $options[$name] );
    else :
        $value = "";
    endif;

    // Generate the table
    if ( !empty($options[$name]) ) :
        $checked = "checked='checked'";
    else :
        $checked = "";
    endif;

    if ( $field == "check" ) {
        $input = "<input type='checkbox' name='smbtoolbar_settings[$name]' id='smbtoolbar_settings[$name]' $checked >";
    } elseif ( $field == "field" ) {
        $input = "<input type='text' name='smbtoolbar_settings[$name]' size='$size' value='$value' placeholder='$placeholder'>";
    }

    // Put table to the variables $out and $help_out
    $out = "<tr valign='top'>
                <th scope='row'>
                    $label
                </th>
                <td>
                    $input
                </td>
            </tr>";
    if ( !empty($help) ) :
        $help_out = "<tr valign='top'>
                        <td></td>
                        <td class='help-text'>
                            $help
                        </td>
                     </tr>";
    else :
        $help_out = "";
    endif;

    // Print the generated table
    echo $out . $help_out;
}

/**
 * Generate the buttons toolbar
 *
 * @since 2.2.1
 */
function smbtoolbar_tollbar() {

    // Read options from BD, sanitiz data and declare variables
    $options = get_option( 'smbtoolbar_settings' );
    $media = $options['media'];

    // Size of icons
    $icon_size = esc_textarea( $options['icon-size'] );
    if (empty($icon_size)) {
        $icon_size = "64";
    }

    // Space between icons
    $margin_right = esc_textarea( $options['margin-right'] );
    if (empty($margin_right)) {
        $margin_right = "10";
    }

    // Open link in new tab
    if (!empty($options['new_tab'])) {
        $new_tab = 'target="blank"';
    } else {
        $new_tab = '';
    }

    // Add a caption above of buttons
    $caption = esc_textarea( $options['caption'] );
    if (empty($caption)) {
        $caption = "";
    }

    // Generate the Buttons
    $metatags_arr[] = '<ul class="smbt-social-icons">';
    if ( !empty($media) ) {
        foreach ($media as $name) {
            foreach ($name as $key => $value) {
                if ($key == "slug") {
                    $slag = $value;
                }
                if ($key == "label") {
                    $label = $value;
                }
                if ($key == "content") {
                    if (!empty($value)) {
                        $icon = plugins_url( "inc/img/social-media-icons/$slag.png", __FILE__ );
                        $metatags_arr[] = '<li>
                                                <a href="' . $value . '" title="' . $label . '" ' . $new_tab . '>
                                                    <img src="' . $icon . '" alt="' . $label . '" />
                                                </a>
                                            </li>';
                    }
                }
            }
        }
    }
    $metatags_arr[] = '</ul>';

    // Add styling for toolbar
    $styles = "<style>
                    .smbt-social-icons {
                        text-align: center;
                    }
                    .smbt-social-icons li {
                        display: inline-block !important;
                        border-bottom: 0 !important;
                        list-style-type: none;
                        -webkit-user-select: none;
                        -moz-user-select: none;
                    }
                    .smbt-social-icons li a {
                        border-bottom: 0 !important;
                        display: inline !important;
                    }
                    .smbt-social-icons li img {
                        width: " . $icon_size . "px;
                        height: " . $icon_size . "px;
                        margin-right: " . $margin_right . "px;
                    }
                </style>";
    
    if ( count( $metatags_arr ) > 0 ) {
        array_unshift( $metatags_arr, $caption );
        array_push( $metatags_arr, $styles );
    }

    // Return the content of array
    return $metatags_arr;
}

/**
 * Create the shortcode "[smbtoolbar]"
 *
 * @since 0.2
 */
function smbtoolbar_shortcode() {
    return implode(PHP_EOL, smbtoolbar_tollbar());
}
add_shortcode( 'smbtoolbar', 'smbtoolbar_shortcode' );

/**
 * Allow shortcodes in the text widget
 *
 * @since 0.2
 */
add_filter( 'widget_text', 'do_shortcode' );

/**
 * Add toolbar to the beginning of each post or/and page.
 *
 * @since 0.2
 */
function smbtoolbar_addContent( $content ) {
    $options = get_option( 'smbtoolbar_settings' );

    if ( is_single() ) {
        if ( !empty($options['show_posts']) && $options['show_posts'] == "on" ) {
            $content = $content . smbtoolbar_shortcode();
        }
    }

    if ( is_page() ) {
        if ( !empty($options['show_pages']) && $options['show_pages'] == "on" ) {
            $content = $content . smbtoolbar_shortcode();
        }
    }

    // Returns the content.
    return $content;
}
add_action( 'the_content', 'smbtoolbar_addContent' );

/**
 * Delete options on uninstall
 *
 * @since 0.1
 */
function smbtoolbar_uninstall() {
    delete_option( 'smbtoolbar_settings' );
}
register_uninstall_hook( __FILE__, 'smbtoolbar_uninstall' );

?>