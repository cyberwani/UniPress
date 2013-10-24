<?php

/**
 * Helper utility functions
 *
 * @package   UniPress
 * @author    João Araújo <unispheredesign@gmail.com>
 * @license   GPL-2.0+
 * @link      http://themeforest.net/user/unisphere
 * @copyright 2013 João Araújo
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Checks if a string starts with a substring
 *
 * @since 1.0.0
 */
function unipress_starts_with( $str, $sub ) {
	$length = strlen( $sub );
	return ( substr( $str, 0, $length ) === $sub );
}

/**
 * Checks if a string ends with a substring
 *
 * @since 1.0.0
 */
function unipress_ends_with( $str, $sub ) {
   return ( substr( $str, strlen( $str ) - strlen( $sub ) ) === $sub );
}

/**
 * Get root parent of a page
 *
 * @since 1.0.0
 */
function get_root_page( $page_id ) 
{
	global $post;

	return ( $post->post_parent ) ? end( get_post_ancestors( $post ) ) : $post->ID;
}