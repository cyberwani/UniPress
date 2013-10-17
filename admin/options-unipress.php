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
		$options[] = array( "name" => "",
							"type" => "heading",
							"location" => 'unipress-fonts-manager' );

			$options[] = array( "name" => "Update available Google web fonts",
								"desc" => "The number of available fonts in the Google web font directory is constantly being updated, hit the \"Update\" button to grab the latest list of fonts. You can preview the available fonts in the <a href=\"http://www.google.com/webfonts\">Google web fonts directory</a>.",
								"id" => "fonts_update_google",
								"type" => "fonts_update_google",
								"std" => "",
								"location" => 'unipress-fonts-manager' );

			$options[] = array( "name" => "Include the following font subsets:",
								"desc" => "NOTE: not all fonts support all subsets, for more information please check the <a href=\"http://www.google.com/webfonts\">Google web fonts directory</a>.",
								"id" => "fonts_subsets_google",
								"std" => "",
								"type" => "multicheck",
								"options" => array( 
									'cyrillic' => 'Cyrillic',
									'cyrillic-ext' => 'Cyrillic Extended',
									'greek' => 'Greek',
									'greek-ext' => 'Greek Extended',
									'latin' => 'Latin',
									'latin-ext' => 'Latin Extended',
									'vietnamese' => 'Vietnamese' ),
								"location" => 'unipress-fonts-manager' );
	}

	// SIDEBAR MANAGER -------------------------------------------------------------------
	$options[] = array( "name" => "",
						"type" => "heading",
						"location" => 'unipress-sidebars-manager' );
							
		$options[] = array( "name" => "Create new custom sidebar:",
							"desc" => "",
							"id" => "sidebar_create",
							"type" => "sidebar_create",
							"class" => "mini",
							"std" => "",
							"location" => 'unipress-sidebars-manager' );

		$options[] = array( "name" => "Available custom sidebars:",
							"desc" => "",
							"id" => "sidebar_list",
							"type" => "sidebar_list",
							"std" => "",
							"location" => 'unipress-sidebars-manager' );

	return $options;
}