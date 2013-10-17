<?php
/**
 * Gets the Google Web Fonts list.
 * If the $remote_update param is true, it forces the update of the font list
 */

function optionsframework_get_google_fonts( $remote_update = false ) {

	// Check if the Google web fonts list has already been imported
	if( false === ( $fonts = get_option('unisphere_google_fonts') ) || $remote_update ) {
		// Import the font list from the remote URI
		$response = wp_remote_get( UNIPRESS_GOOGLE_WEB_FONTS_FILE );
		if( is_wp_error( $response ) ) {
			// Lets try reading the local file
			$response = wp_remote_get( trailingslashit( UNIPRESS_ADMIN_URI ) . 'fonts/fonts.json' );
			if( is_wp_error( $response ) ) {
				return null;
			}
		} 

		// Parse the JSON data into a PHP array
		$json_data = json_decode( $response['body'], true );
		
		$fonts = array();
		foreach( $json_data['items'] as $font ) {
			if( false === array_search( 'khmer', $font['subsets'] ) ) {
				$fonts[] = array( 'value' => 'google:' . str_replace(' ', '+', $font['family']), 'text' => $font['family'] );
			}
		}

		update_option( 'unisphere_google_fonts', $fonts );
	}

	return $fonts;
}