<?php
/**
 * Plugin Name: UniPress
 * Plugin URI:  https://github.com/unisphere/UniPress
 * Description: A foundation off of which to develop WordPress themes that adhere to the ThemeForest rules and standards.
 * Version:     1.0.0 Alpha
 * Author:      João Araújo
 * Author URI:  http://themeforest.net/user/unisphere
 * Text Domain: unipress
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 *
 * UniPress - A WordPress theme development framework.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write 
 * to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package   UniPress
 * @author    João Araújo <unispheredesign@gmail.com>
 * @license   GPL-2.0+
 * @link      http://themeforest.net/user/unisphere
 * @copyright 2013 João Araújo
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The UniPress class launches the framework.  It's the organizational structure behind the entire framework. 
 *
 * @since 1.0.0
 */
final class UniPress {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	private static $instance = null;

	/**
	 * The admin panel settings.
	 *
	 * @since    1.0.0
	 *
	 * @var      array
	 */
	private static $settings = null;

	/**
	 * Constructor method for the UniPress class.  This method adds other methods of the class to 
	 * specific hooks within WordPress.  It controls the load order of the required files for running 
	 * the framework.
	 *
	 * @since	1.0.0
	 */
	private function __construct() {

		// Define framework constants.
		add_action( 'after_setup_theme', array( &$this, 'constants' ), 1 );

		// Add theme features that are enabled by default.
		add_action( 'after_setup_theme', array( &$this, 'default_features' ), 2 );

		// Load the framework functions.
		add_action( 'after_setup_theme', array( &$this, 'functions' ), 4 );

		// Language functions and translations setup.
		add_action( 'after_setup_theme', array( &$this, 'load_plugin_textdomain' ), 5 );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Defines the constants for use within the framework.  
	 *
	 * @since	1.0.0
	 */
	public function constants() {

		// Sets the framework version number.
		define( 'UNIPRESS_VERSION', self::VERSION );

		// Sets the remote file containing the latest Google Web Fonts list
		define( 'UNIPRESS_GOOGLE_WEB_FONTS_FILE', 'http://notifier.unispheredesign.com/unipress/update-font-list.php' );

		// Sets the path to the parent theme directory.
		define( 'UNIPRESS_THEME_DIR', get_template_directory() );

		// Sets the path to the parent theme directory URI.
		define( 'UNIPRESS_THEME_URI', get_template_directory_uri() );

		// Sets the path to the child theme directory.
		define( 'UNIPRESS_CHILD_THEME_DIR', get_stylesheet_directory() );

		// Sets the path to the child theme directory URI.
		define( 'UNIPRESS_CHILD_THEME_URI', get_stylesheet_directory_uri() );

		// Sets the path to the framework directory.
		define( 'UNIPRESS_DIR', dirname( __FILE__ ) );

		// Sets the path to the framework directory URI.
		define( 'UNIPRESS_URI', trailingslashit( plugins_url() ) . basename( dirname( __FILE__ ) ) );

		// Sets the path to the framework functions directory.
		define( 'UNIPRESS_FUNCTIONS', trailingslashit( UNIPRESS_DIR ) . 'functions' );

		// Sets the path to the framework languages directory.
		define( 'UNIPRESS_LANGUAGES', trailingslashit( UNIPRESS_DIR ) . 'languages' );

		// Sets the path to the framework images directory URI.
		define( 'UNIPRESS_IMAGES', trailingslashit( UNIPRESS_URI ) . 'images' );

		// Sets the path to the framework CSS directory URI.
		define( 'UNIPRESS_CSS', trailingslashit( UNIPRESS_URI ) . 'css' );

		// Sets the path to the framework JavaScript directory URI.
		define( 'UNIPRESS_JS', trailingslashit( UNIPRESS_URI ) . 'js' );

		// Sets the path to the framework admin directory.
		define( 'UNIPRESS_ADMIN_DIR', trailingslashit( UNIPRESS_DIR ) . 'admin' );

		// Sets the path to the framework admin directory URI.
		define( 'UNIPRESS_ADMIN_URI', trailingslashit( UNIPRESS_URI ) . 'admin' );

		// Sets the path to the framework admin images directory URI.
		define( 'UNIPRESS_ADMIN_IMAGES', trailingslashit( UNIPRESS_ADMIN_URI ) . 'images' );

		// Sets the path to the framework admin CSS directory URI.
		define( 'UNIPRESS_ADMIN_CSS', trailingslashit( UNIPRESS_ADMIN_URI ) . 'css' );

		// Sets the path to the framework admin JavaScript directory URI.
		define( 'UNIPRESS_ADMIN_JS', trailingslashit( UNIPRESS_ADMIN_URI ) . 'js' );
	}

	/**
	 * Adds default theme features. The theme using UniPress can disable them if not needed.
	 * This is to prevent features like Portfolio custom post type, Shortcodes or Sidebar manager
	 * to become unavailable when user switches themes.
	 *
	 * @since	1.0.0
	 */
	public function default_features() {

		// Adds default support for the custom sidebars (admin panel and meta box)
		add_theme_support( 'unipress-sidebars' );

		// Adds default support for the Portfolio custom post type
		add_theme_support( 'unipress-post-type-portfolio' );
	}

	/**
	 * Loads the framework functions.  Many of these functions are needed to properly run the 
	 * framework. Some components are only loaded if the theme supports them.
	 *
	 * @since    1.0.0
	 */
	public function functions() {

		// Load helper utility functions
		require_once( trailingslashit( UNIPRESS_FUNCTIONS ) . 'helpers.php' );

		// Load Google Web Fonts feature if supported
		require_if_theme_supports( 'unipress-fonts', trailingslashit( UNIPRESS_FUNCTIONS ) . 'fonts.php' );

		// Load the admin panel
		require_once( trailingslashit( UNIPRESS_ADMIN_DIR ) . 'options-framework.php' );

		// Load the Portfolio custom post type (themes can disable it)
		require_if_theme_supports( 'unipress-post-type-portfolio', trailingslashit( UNIPRESS_FUNCTIONS ) . 'post-type-portfolio.php' );

		// Load meta boxes (custom fields)
		require_once( trailingslashit( UNIPRESS_FUNCTIONS ) . 'custom-fields.php' );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		// TODO
	}

	/**
	 * Gets the admin panel stored settings
	 *
	 * @since    1.0.0
	 */
	public static function settings() {

		if ( null == self::$settings ) {			
			$optionsframework_settings = get_option( 'optionsframework' );

			// Gets the unique option id
			if ( isset( $optionsframework_settings['id'] ) ) {
				$option_name = $optionsframework_settings['id'];
			}
			else {
				$option_name = 'unipress';
			};

			self::$settings = get_option($option_name);
		}

		return self::$settings;
	}
}

/**
 * The main function responsible for returning the UniPress instance to use everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $unipress = UniPress(); ?>
 *
 * @since 	1.0.0
 *
 * @return 	object 	The instance of the UniPress class
 */
function UniPress() {
	return UniPress::instance();
}

/**
 * The main function responsible for returning the UniPress admin panel stored settings.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $settings = UniPressSettings(); ?>
 *
 * @since 	1.0.0
 *
 * @return 	object 	An array containing the admin panel settings
 */
function UniPressSettings() {
	$unipress = UniPress();
	return $unipress::settings();
}

// Start, hammer time!
UniPress();