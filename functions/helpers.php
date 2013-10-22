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

/**
 * Checks if a string starts with a specific value
 *
 * @since 1.0.0
 */
function unipress_starts_with( $str, $sub ) {
	$length = strlen( $sub );
	return ( substr( $str, 0, $length ) === $sub );
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