<?php
/*
Description: A framework for building theme options.
Author: Devin Price
Author URI: http://www.wptheming.com
License: GPLv2
Version: 1.6
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/* If the user can't edit theme options, no use running this plugin */

add_action( 'init', 'optionsframework_rolescheck' );

function optionsframework_rolescheck () {
	if ( current_user_can( 'edit_theme_options' ) ) {
		// If the user can edit theme options, let the fun begin!
		add_action( 'admin_menu', 'optionsframework_add_page');
		add_action( 'admin_init', 'optionsframework_init' );
	}
}

/* Loads the file for option sanitization */

add_action( 'init', 'optionsframework_load_sanitization' );

function optionsframework_load_sanitization() {
	require_once dirname( __FILE__ ) . '/options-sanitize.php';
}

/*
 * Creates the settings in the database by looping through the array
 * we supplied in options.php.  This is a neat way to do it since
 * we won't have to save settings for headers, descriptions, or arguments.
 *
 * Read more about the Settings API in the WordPress codex:
 * http://codex.wordpress.org/Settings_API
 *
 */

function optionsframework_init() {

	// Include the required files
	require_once dirname( __FILE__ ) . '/options-interface.php';
	require_once dirname( __FILE__ ) . '/options-media-uploader.php';

	// Optionally Loads the options file from the theme
	$location = apply_filters( 'options_framework_location', array( 'options.php' ) );
	$optionsfile = locate_template( $location );

	// Load settings
	$optionsframework_settings = get_option('optionsframework' );

	// Updates the unique option id in the database if it has changed
	if ( function_exists( 'optionsframework_option_name' ) ) {
		optionsframework_option_name();
	}
	elseif ( has_action( 'optionsframework_option_name' ) ) {
		do_action( 'optionsframework_option_name' );
	}
	// If the developer hasn't explicitly set an option id, we'll use a default
	else {
		$default_themename = get_option( 'stylesheet' );
		$default_themename = preg_replace("/\W/", "_", strtolower($default_themename) );
		$default_themename = 'optionsframework_' . $default_themename;
		if ( isset( $optionsframework_settings['id'] ) ) {
			if ( $optionsframework_settings['id'] == $default_themename ) {
				// All good, using default theme id
			} else {
				$optionsframework_settings['id'] = $default_themename;
				update_option( 'optionsframework', $optionsframework_settings );
			}
		}
		else {
			$optionsframework_settings['id'] = $default_themename;
			update_option( 'optionsframework', $optionsframework_settings );
		}
	}

	// If the option has no saved data, load the defaults
	if ( ! get_option( $optionsframework_settings['id'] ) ) {
		optionsframework_setdefaults();
	}

	// Registers the settings fields and callback
	register_setting( 'optionsframework', $optionsframework_settings['id'], 'optionsframework_validate' );
	// Change the capability required to save the 'optionsframework' options group.
	add_filter( 'option_page_capability_optionsframework', 'optionsframework_page_capability' );
}

/**
 * Ensures that a user with the 'edit_theme_options' capability can actually set the options
 * See: http://core.trac.wordpress.org/ticket/14365
 *
 * @param string $capability The capability used for the page, which is manage_options by default.
 * @return string The capability to actually use.
 */

function optionsframework_page_capability( $capability ) {
	return 'edit_theme_options';
}

/*
 * Adds default options to the database if they aren't already present.
 * May update this later to load only on plugin activation, or theme
 * activation since most people won't be editing the options.php
 * on a regular basis.
 *
 * http://codex.wordpress.org/Function_Reference/add_option
 *
 */

function optionsframework_setdefaults() {

	$optionsframework_settings = get_option( 'optionsframework' );

	// Gets the unique option id
	$option_name = $optionsframework_settings['id'];

	/*
	 * Each theme will hopefully have a unique id, and all of its options saved
	 * as a separate option set.  We need to track all of these option sets so
	 * it can be easily deleted if someone wishes to remove the plugin and
	 * its associated data.  No need to clutter the database.
	 *
	 */

	if ( isset( $optionsframework_settings['knownoptions'] ) ) {
		$knownoptions =  $optionsframework_settings['knownoptions'];
		if ( !in_array( $option_name, $knownoptions ) ) {
			array_push( $knownoptions, $option_name );
			$optionsframework_settings['knownoptions'] = $knownoptions;
			update_option( 'optionsframework', $optionsframework_settings );
		}
	} else {
		$newoptionname = array( $option_name );
		$optionsframework_settings['knownoptions'] = $newoptionname;
		update_option( 'optionsframework', $optionsframework_settings );
	}

	// Gets the default options data from the array in options.php
	$options =& _optionsframework_options();

	// If the options haven't been added to the database yet, they are added now
	$values = of_get_default_values( optionsframework_get_current_location() );

	if ( isset( $values ) ) {
		add_option( $option_name, $values ); // Add option with default settings
	}
}

/* Define menu options
 *
 * Example usage:
 *
 * add_filter( 'optionsframework_menu', function($menu) {
 *		// Change parent menu string
 *		$menu['menu_title'] = 'Twenty Thirteen';
 *
 *		// Add more sub-menus
 *		$menu['sub-menus'][] = array(
 *			'page_title' => 'Skins Manager',
 *			'menu_title' => 'Skins Manager',
 *			'capability' => 'edit_theme_options',
 *			'menu_slug' => 'unipress-skins-manager',
 *		);
 +
 *		return $menu;
 * });
 */

function optionsframework_menu_settings() {

	$menu = array(
		'page_title' => __( 'Theme Options', 'unipress' ),
		'menu_title' => 'UniPress',
		'capability' => 'edit_theme_options',
		'menu_slug' => 'unipress-theme-options',
		'callback' => 'optionsframework_page',
		'icon' => trailingslashit( UNIPRESS_ADMIN_IMAGES ) . 'icon-menu.png'
	);

	$menu['sub-menus'] = array();

	$menu['sub-menus']['unipress-theme-options'] = array(
		'page_title' => __( 'Theme Options', 'unipress' ),
		'menu_title' => __( 'Theme options', 'unipress' ),
		'capability' => 'edit_theme_options',
		'menu_slug' => 'unipress-theme-options',
		'callback' => 'optionsframework_page'
	);

	// An hook into the menu is available
	$menu = apply_filters( 'optionsframework_menu', $menu );

	// Only include font manager if the current theme supports it
	if( current_theme_supports( 'unipress-fonts' ) ) {
		$menu['sub-menus']['unipress-fonts-manager'] = array(
			'page_title' => __( 'Fonts Manager', 'unipress' ),
			'menu_title' => __( 'Fonts manager', 'unipress' ),
			'capability' => 'edit_theme_options',
			'menu_slug' => 'unipress-fonts-manager',
			'callback' => 'optionsframework_page'
		);
	}
	
	$menu['sub-menus']['unipress-sidebars-manager'] = array(
		'page_title' => __( 'Sidebars Manager', 'unipress' ),
		'menu_title' => __( 'Sidebars manager', 'unipress' ),
		'capability' => 'edit_theme_options',
		'menu_slug' => 'unipress-sidebars-manager',
		'callback' => 'optionsframework_page'
	);

	return $menu;
}

/* Adds main and sub pages to the WordPress admin panel. */

function optionsframework_add_page() {

	// Include the required files
	require_once dirname( __FILE__ ) . '/options-unipress.php';

	// Get options
	$options =& _optionsframework_options(); 

	$menu = optionsframework_menu_settings();

	// See if the UniPress default options are the only ones available and change the parent menu slug.
	if( isset( $options['unipress-options-only'] ) ) {
		// Remove the "Theme Options" sub-menu
		unset( $menu['sub-menus']['unipress-theme-options'] );

		// By default set the "Sidebars Manager" as the parent menu slug
		$menu['menu_slug'] = $menu['sub-menus']['unipress-sidebars-manager']['menu_slug'];

		if( current_theme_supports( 'unipress-fonts' ) ) {
			$menu['menu_slug'] = $menu['sub-menus']['unipress-fonts-manager']['menu_slug'];
		}
	}

	// Parent menu
	$of_page = add_menu_page( $menu['page_title'], $menu['menu_title'], $menu['capability'], $menu['menu_slug'], $menu['callback'], $menu['icon'] ); 

	// Load the required CSS
	add_action( 'admin_print_styles-' . $of_page, 'optionsframework_load_styles' );

	foreach ($menu['sub-menus'] as $sub_menu) {
		// Sub menu
		$of_page = add_submenu_page( $menu['menu_slug'], $sub_menu['page_title'], $sub_menu['menu_title'], $sub_menu['capability'], $sub_menu['menu_slug'], $sub_menu['callback'] );

		// Load the required CSS
		add_action( 'admin_print_styles-' . $of_page, 'optionsframework_load_styles' );		
	}

	// Load the required javascript
	add_action( 'admin_enqueue_scripts', 'optionsframework_load_scripts' );
}

/* Loads the CSS */

function optionsframework_load_styles() {
	wp_enqueue_style( 'optionsframework', trailingslashit( UNIPRESS_ADMIN_URI ).'css/optionsframework.css' );
	if ( !wp_style_is( 'wp-color-picker','registered' ) ) {
		wp_register_style( 'wp-color-picker', trailingslashit( UNIPRESS_ADMIN_URI ).'css/color-picker.min.css' );
	}
	wp_enqueue_style( 'wp-color-picker' );
}

/* Loads the javascript */

function optionsframework_load_scripts( $hook ) {

	$menu = optionsframework_menu_settings();

	// Enqueue colorpicker scripts for versions below 3.5 for compatibility
	if ( !wp_script_is( 'wp-color-picker', 'registered' ) ) {
		wp_register_script( 'iris', trailingslashit( UNIPRESS_ADMIN_URI ) . 'js/iris.min.js', array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );
		wp_register_script( 'wp-color-picker', trailingslashit( UNIPRESS_ADMIN_URI ) . 'js/color-picker.min.js', array( 'jquery', 'iris' ) );
		$colorpicker_l10n = array(
			'clear' => __( 'Clear','unipress' ),
			'defaultString' => __( 'Default', 'unipress' ),
			'pick' => __( 'Select Color', 'unipress' )
		);
		wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );
	}

	// Enqueue custom option panel JS
	wp_enqueue_script( 'options-custom', trailingslashit( UNIPRESS_ADMIN_URI ) . 'js/options-custom.js', array( 'jquery','wp-color-picker' ) );

	// Inline scripts from options-interface.php
	add_action( 'admin_head', 'of_admin_head' );
}

function of_admin_head() {
	// Hook to add custom scripts
	do_action( 'optionsframework_custom_scripts' );
}

/*
 * Returns page title based on the current page slug
 */
function optionsframework_get_page_title() {
	$menu = optionsframework_menu_settings();

	foreach ($menu['sub-menus'] as $sub_menu) {
		if( $_GET['page'] == $sub_menu['menu_slug'] ) {
			return $sub_menu['page_title'];
		}
	}
}

/*
 * Returns current location
 */
function optionsframework_get_current_location() {
	return $_GET['page'];
}

/*
 * Builds out the options panel.
 *
 * If we were using the Settings API as it was likely intended we would use
 * do_settings_sections here.  But as we don't want the settings wrapped in a table,
 * we'll call our own custom optionsframework_fields.  See options-interface.php
 * for specifics on how each individual field is generated.
 *
 * Nonces are provided using the settings_fields()
 *
 */
if ( !function_exists( 'optionsframework_page' ) ) :
function optionsframework_page() { ?>

	<div id="optionsframework-wrap" class="wrap optionsframework-<?php echo optionsframework_get_current_location(); ?>">
	<?php screen_icon( 'themes' ); ?>
	<h2><?php echo optionsframework_get_page_title(); ?></h2>
	<?php settings_errors( 'options-framework' ); ?>
	<h2 class="nav-tab-wrapper">
		<?php echo optionsframework_tabs(); ?>
	</h2>
	<div id="optionsframework-metabox" class="metabox-holder">
		<div id="optionsframework" class="postbox">
			<form action="options.php" method="post">
			<?php settings_fields( 'optionsframework' ); ?>
			<?php optionsframework_fields(); /* Settings */ ?>
			<div id="optionsframework-submit">
				<input type="hidden" name="location" value="<?php echo optionsframework_get_current_location(); ?>" />
				<input type="submit" class="button-primary" name="update" value="<?php esc_attr_e( 'Save Options', 'unipress' ); ?>" />
				<input type="submit" class="reset-button button-secondary" name="reset" value="<?php esc_attr_e( 'Restore Defaults', 'unipress' ); ?>" onclick="return confirm( '<?php print esc_js( __( 'Click OK to reset. Any theme settings will be lost!', 'unipress' ) ); ?>' );" />
				<div class="clear"></div>
			</div>
			</form>
		</div> <!-- / #container -->
	</div>
	<?php do_action( 'optionsframework_after' ); ?>
	</div> <!-- / .wrap -->

<?php
}
endif;

/**
 * Validate Options.
 *
 * This runs after the submit/reset button has been clicked and
 * validates the inputs.
 *
 * @uses $_POST['reset'] to restore default options
 * @uses $_POST['location'] to get the current option panel
 */
function optionsframework_validate( $input ) {

	/*
	 * Create a new sidebar
	 */

	if ( isset( $_POST['sidebar_create'] ) ) {
		$new_sidebar_name = $input['sidebar_create_text'];

		// Check for an existing sidebar with the same name
		$sidebar_exists = false;
		if( ! empty( $input['sidebar_list'] ) ) {
			foreach( $input['sidebar_list'] as $elementKey => $element ) {
				foreach( $element as $valueKey => $value ) {
					if( $valueKey == 'name' && $value == $new_sidebar_name ) {
						$sidebar_exists = true;
					}
				}
			}
		}

		if( trim( $new_sidebar_name ) != '' && ! $sidebar_exists ) 
			$input['sidebar_list'][] = Array( 'name' => sanitize_text_field( $new_sidebar_name ), 'id' => sanitize_title( $new_sidebar_name ) );
		else {
			$_POST['sidebar_exists'] = 'true';
		}
	}

	/*
	 * Delete a sidebar
	 */

	if ( isset( $_POST['sidebar_delete'] ) ) {
		foreach( $input['sidebar_list'] as $elementKey => $element ) {
			foreach( $element as $valueKey => $value ) {
				if( $valueKey == 'id' && $value == $_POST['sidebar_delete'] ) {
					unset( $input['sidebar_list'][$elementKey] );
				} 
			}
		}
	}

	/*
	 * Update Google Web Fonts from the remote list
	 */
	if( isset( $_POST['fonts_update_google'] ) ) {
		optionsframework_get_google_fonts(true);
	}

	/*
	 * Restore Defaults.
	 *
	 * In the event that the user clicked the "Restore Defaults"
	 * button, the options defined in the theme's options.php
	 * file will be added to the option for the active theme.
	 */

	if ( isset( $_POST['reset'] ) ) {
		add_settings_error( 'options-framework', 'restore_defaults', __( 'Default options restored.', 'unipress' ), 'updated fade' );
		return of_get_default_values( $_POST['location'] );
	}

	/*
	 * Update Settings
	 *
	 * This used to check for $_POST['update'], but has been updated
	 * to be compatible with the theme customizer introduced in WordPress 3.4
	 */

	$clean = array();
	$options =& _optionsframework_options();
	foreach ( $options as $option ) {

		if ( ! isset( $option['id'] ) ) {
			continue;
		}

		if ( ! isset( $option['type'] ) ) {
			continue;
		}

		$id = preg_replace( '/[^a-zA-Z0-9._\-]/', '', strtolower( $option['id'] ) );

		if ( ! isset( $option['location'] ) || $option['location'] != $_POST['location'] ) {
			// If the current location doesn't match the option, keep existing value
			$clean[$id] = of_get_option( $id );
		} else {
			// Set checkbox to false if it wasn't sent in the $_POST
			if ( 'checkbox' == $option['type'] && ! isset( $input[$id] ) ) {
				$input[$id] = false;
			}

			// Set each item in the multicheck to false if it wasn't sent in the $_POST
			if ( 'multicheck' == $option['type'] && ! isset( $input[$id] ) ) {
				foreach ( $option['options'] as $key => $value ) {
					$input[$id][$key] = false;
				}
			}

			// If deleting or creating a sidebar bypass sanitization since we have already done it above
			if ( ( isset( $_POST['sidebar_create'] ) || isset( $_POST['sidebar_delete'] ) ) && $id == 'sidebar_list' ) {
				$clean['sidebar_list'] = $input['sidebar_list'];
				
				// Output an error in case the sidebar name is empty or already exists
				if( isset( $_POST['sidebar_exists'] ) ) {
					add_settings_error( 'options-framework', 'sidebar_exists', __( 'There\'s already a sidebar with that name or the field is empty.', 'unipress' ), 'error' );
					remove_action( 'optionsframework_after_validate', 'optionsframework_save_options_notice' );
				}
			} else {
				// For a value to be submitted to database it must pass through a sanitization filter
				if ( has_filter( 'of_sanitize_' . $option['type'] ) ) {
					$clean[$id] = apply_filters( 'of_sanitize_' . $option['type'], $input[$id], $option );
				}
			}
		}
	}

	// Hook to run after validation
	do_action( 'optionsframework_after_validate', $clean );

	return $clean;
}

/**
 * Display message when options have been saved
 */

function optionsframework_save_options_notice() {
	add_settings_error( 'options-framework', 'save_options', __( 'Options saved.', 'unipress' ), 'updated fade' );
}

add_action( 'optionsframework_after_validate', 'optionsframework_save_options_notice' );

/**
 * Format Configuration Array.
 *
 * Get an array of all default values as set in
 * options.php. The 'id','std' and 'type' keys need
 * to be defined in the configuration array. In the
 * event that these keys are not present the option
 * will not be included in this function's output.
 *
 * @return    array     Rey-keyed options configuration array.
 *
 * @access    private
 */

function of_get_default_values( $location ) {
	$output = array();
	$config =& _optionsframework_options();
	foreach ( (array) $config as $option ) {
		if ( ! isset( $option['id'] ) ) {
			continue;
		}
		if ( ! isset( $option['std'] ) ) {
			continue;
		}
		if ( ! isset( $option['type'] ) ) {
			continue;
		}
		if ( isset( $option['location'] ) && $option['location'] == $location ) {
			if ( has_filter( 'of_sanitize_' . $option['type'] ) ) {
				$output[$option['id']] = apply_filters( 'of_sanitize_' . $option['type'], $option['std'], $option );
			} 
		} else {
			// Keep the other theme admin pages fields intact
			$output[$option['id']] = of_get_option( $option['id'] );
		}
	}
	return $output;
}

/**
 * Wrapper for optionsframework_options()
 *
 * Allows for manipulating or setting options via 'of_options' filter
 * For example:
 *
 * <code>
 * add_filter('of_options', function($options) {
 *     $options[] = array(
 *         'name' => 'Input Text Mini',
 *         'desc' => 'A mini text input field.',
 *         'id' => 'example_text_mini',
 *         'std' => 'Default',
 *         'class' => 'mini',
 *         'type' => 'text'
 *     );
 *
 *     return $options;
 * });
 * </code>
 *
 * Also allows for setting options via a return statement in the
 * options.php file.  For example (in options.php):
 *
 * <code>
 * return array(...);
 * </code>
 *
 * @return array (by reference)
 */
function &_optionsframework_options() {
	static $options = null;

	if ( !$options ) {
		// Load options from options.php file (if it exists)
		$location = apply_filters( 'options_framework_location', array('options.php') );
		if ( $optionsfile = locate_template( $location ) ) {
			$maybe_options = require_once $optionsfile;
			if ( is_array($maybe_options) ) {
				$options = $maybe_options;
			} else if ( function_exists( 'optionsframework_options' ) ) {
				$options = optionsframework_options();
			}
		}

		// Allow setting/manipulating options via filters
		$options = apply_filters('of_options', $options);
	}

	// Append the framework options
	if( is_array( $options ) ) {
		$combined_options = array_merge( $options, optionsframework_get_unipress_options() );
	} else {
		$combined_options = optionsframework_get_unipress_options();
		$combined_options['unipress-options-only'] = true;
	}

	return $combined_options;
}

/**
 * Get Option.
 *
 * Helper function to return the theme option value.
 * If no value has been saved, it returns $default.
 * Needed because options are saved as serialized strings.
 */

if ( ! function_exists( 'of_get_option' ) ) {

	function of_get_option( $name, $default = false ) {
		$config = get_option( 'optionsframework' );

		if ( ! isset( $config['id'] ) ) {
			return $default;
		}

		$options = get_option( $config['id'] );

		if ( isset( $options[$name] ) ) {
			return $options[$name];
		}

		return $default;
	}
}