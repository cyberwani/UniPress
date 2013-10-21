<?php 
/**
 * These are the options related to the Fonts Manager and Sidebars Manager.
 * They are not filterable and are always added by the framework.
 *
 * @since 1.0.0
 */

function optionsframework_get_unipress_options() {

	$options = array();

	// Only include font options if the current theme supports it
	if( current_theme_supports( 'unipress-fonts' ) ) {

		// FONTS MANAGER -------------------------------------------------------------------
		$options[] = array( 'name' => '',
							'type' => 'heading',
							'location' => 'unipress-fonts-manager' );

			$options[] = array( 'name' => __( 'Update available Google web fonts', 'unipress' ),
								'desc' => __( 'The number of available fonts in the Google web font directory is constantly being updated, hit the "Update" button to grab the latest list of fonts. You can preview the available fonts in the <a href="http://www.google.com/webfonts">Google web fonts directory</a>.', 'unipress' ),
								'id' => 'fonts_update_google',
								'type' => 'fonts_update_google',
								'std' => '',
								'location' => 'unipress-fonts-manager' );

			$options[] = array( 'name' => __( 'Include the following font subsets:', 'unipress' ),
								'desc' => __( 'NOTE: not all fonts support all subsets, for more information please check the <a href="http://www.google.com/webfonts">Google web fonts directory</a>.', 'unipress' ),
								'id' => 'fonts_subsets_google',
								'std' => '',
								'type' => 'multicheck',
								'options' => array( 
									'cyrillic' => __( 'Cyrillic', 'unipress' ),
									'cyrillic-ext' => __( 'Cyrillic Extended', 'unipress' ),
									'greek' => __( 'Greek', 'unipress' ),
									'greek-ext' => __( 'Greek Extended', 'unipress' ),
									'latin' => __( 'Latin', 'unipress' ),
									'latin-ext' => __( 'Latin Extended', 'unipress' ),
									'vietnamese' => __( 'Vietnamese', 'unipress' ) ),
								'location' => 'unipress-fonts-manager' );
	}

	// SIDEBAR MANAGER -------------------------------------------------------------------
	$options[] = array( 'name' => '',
						'type' => 'heading',
						'location' => 'unipress-sidebars-manager' );
							
		$options[] = array( 'name' => __( 'Create new custom sidebar:', 'unipress' ),
							'desc' => '',
							'id' => 'sidebar_create',
							'type' => 'sidebar_create',
							'class' => 'mini',
							'std' => '',
							'location' => 'unipress-sidebars-manager' );

		$options[] = array( 'name' => __( 'Available custom sidebars:', 'unipress' ),
							'desc' => '',
							'id' => 'sidebar_list',
							'type' => 'sidebar_list',
							'std' => '',
							'location' => 'unipress-sidebars-manager' );

	// Only include import/export options if the current theme supports it
	if( current_theme_supports( 'unipress-import-export' ) ) {

		// IMPORT/EXPORT -------------------------------------------------------------------
		$options[] = array( 'name' => '',
							'type' => 'heading',
							'location' => 'unipress-import-export' );
							
			$options[] = array( 'name' => __( 'Import Settings', 'unipress' ),
								'desc' => __( 'Paste an exported encoded field from another theme installation into this field in order to import the other theme installation settings.', 'unipress' ),
								'id' => 'import_settings',
								'std' => '',
								'type' => 'textarea',
								'location' => 'unipress-import-export' ); 

			$options[] = array( 'name' => __( 'Export Settings', 'unipress' ),
								'desc' => __( 'Copy this encoded field into the "Import Settings" field in another theme installation to import this installation settings.', 'unipress' ),
								'id' => 'export_settings',
								'std' => '',
								'type' => 'textarea',
								'location' => 'unipress-import-export' ); 
	}

	return $options;
}