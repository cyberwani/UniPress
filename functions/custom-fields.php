<?php
/**
 * A framework for building meta boxes (custom fields).
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
 * These are the options regarding the Sidebars custom fields.
 * They are always added by the framework but themes can disable them.
 *
 * @since	1.0.0
 */
function unipress_get_default_custom_fields() {

	$fields = array();
	
	// Only include sidebar custom fields if the current theme supports it
	if( current_theme_supports( 'unipress-sidebars' ) ) {

		// SIDEBAR -------------------------------------------------------------------
		$fields[] = array(	'name' => '_page_toggle_start_sidebar',
							'std' => '',
							'title' => 'Sidebar',
							'description' => '',
							'type' => 'toggle-start',
							'location' => array( 'Page', 'Post', 'Portfolio' ) );

			$fields[] = array(	'name' => '_page_custom_sidebar',
								'std' => '',
								'title' => 'Custom sidebar',
								'description' => 'Select the sidebar you wish to display on this page, and which sidebar it will replace.<br/>If you haven\'t created custom sidebars yet, you can do it now: <a href="admin.php?page=unipress-sidebars-manager">create custom sidebar +</a>',
								'type' => 'sidebar',
								'location' => array( 'Page', 'Post', 'Portfolio' ) );

			$fields[] = array(	'name' => '_page_toggle_stop_sidebar',
								'std' => '',
								'title' => '',
								'description' => '',
								'type' => 'toggle-end',
								'location' => array( 'Page', 'Post', 'Portfolio' ) );
	}

	return $fields;
}


/**
 * Creates a meta box in the "Page" post type
 *
 * @since	1.0.0
 */
function unipress_new_meta_boxes_page() {
	unipress_new_meta_boxes('Page');
}


/**
 * Creates a meta box in the "Post" post type
 *
 * @since	1.0.0
 */
function unipress_new_meta_boxes_post() {
	unipress_new_meta_boxes('Post');
}


/**
 * Creates a meta box in the "Slider" custom post type
 *
 * @since	1.0.0
 */
function unipress_new_meta_boxes_slider() {
	unipress_new_meta_boxes('Slider');
}


/**
 * Creates a meta box in the "Portfolio" custom post type
 *
 * @since	1.0.0
 */
function unipress_new_meta_boxes_portfolio() {
	unipress_new_meta_boxes('Portfolio');
}


/**
 * Inits the creation of meta boxes for the several post types
 *
 * @since	1.0.0
 */
function unipress_meta_box_init() {
	add_meta_box( 'unipress_new_meta_boxes_post', __( 'UniPress Post Settings', 'unipress'), 'unipress_new_meta_boxes_post', 'post', 'normal', 'high' );
	add_meta_box( 'unipress_new_meta_boxes_page', __( 'UniPress Page Settings', 'unipress'), 'unipress_new_meta_boxes_page', 'page', 'normal', 'high' );
	add_meta_box( 'unipress_new_meta_boxes_slider', __( 'UniPress Slider Settings', 'unipress'), 'unipress_new_meta_boxes_slider', 'slider', 'normal', 'high' );
	add_meta_box( 'unipress_new_meta_boxes_portfolio', __( 'UniPress Portfolio Settings', 'unipress'), 'unipress_new_meta_boxes_portfolio', 'portfolio_cpt', 'normal', 'high' );
}
add_action( 'admin_menu', 'unipress_meta_box_init' );


/**
 * Displays the meta box
 *
 * @since	1.0.0
 */
function unipress_new_meta_boxes( $type ) {

	global $post;
	$new_meta_boxes =& _unipress_custom_fields();
	
	// Use nonce for verification
    echo '<input type="hidden" name="unisphere_meta_box_nonce" value="', wp_create_nonce( basename( __FILE__ ) ), '" />';

 	echo '<div id="custom-fields-form-wrap">';

	foreach ( $new_meta_boxes as $meta_box ) {
		if ( ( is_array( $meta_box['location'] ) && in_array( $type, $meta_box['location'] ) ) || ( ! is_array( $meta_box['location'] ) && $meta_box['location'] == $type ) ) {
			if ( $meta_box['type'] == 'title' ) {
				echo '<p style="font-size: 18px; font-weight: bold; font-style: normal; color: #e5e5e5; text-shadow: 0 1px 0 #111; line-height: 40px; background-color: #464646; border: 1px solid #111; padding: 0 10px; -moz-border-radius: 6px;">' . $meta_box['title'] . '</p>';
			} elseif ( $meta_box['type'] == 'toggle-start' ) { // Start toggle
				echo '<div class="toggle-container">';
				echo '<a class="toggle" href="javascript:void(0);"><span class="toggle-sign">+</span><span class="toggle-title">' . $meta_box['title'] . '</span></a>';
				echo '<div class="toggle-content" style="display: none;">';
			} elseif ( $meta_box['type'] == 'toggle-end' ) { // End toggle
				echo '</div></div>';
			} else {
				$meta_box_value = get_post_meta( $post->ID, $meta_box['name'], true );
		
				if ( $meta_box_value == "" )
					$meta_box_value = $meta_box['std'];

				echo '<div id="section' . $meta_box['name'] . '" class="section section-' . $meta_box['type'] . ' ' . ( !empty($meta_box['class']) ? $meta_box['class'] : '' ) . '">';
				
				switch ( $meta_box['type'] ) {
					case 'text':
						echo '<h4 class="heading">' . $meta_box['title'] . '</h4>';
						echo '<div class="option">';
						echo '<div class="controls">';
						echo '<input type="text" id="' . $meta_box['name'] . '" name="' . $meta_box['name'] . '" value="' . htmlspecialchars( $meta_box_value ) . '" class="of-input" />';
						echo '</div>';
						echo '<div class="explain explain-' . $meta_box['type'] . '">' . $meta_box['description'] . '</div>';
						echo '<div class="clear"></div>';
						echo '</div>';
						break;
						
					case 'checkbox':
						if($meta_box_value == '1'){ $checked = "checked=\"checked\""; }else{ $checked = "";} 
						echo '<h4 class="heading">' . $meta_box['title'] . '</h4>';
						echo '<div class="option">';
						echo '<div class="controls">';
						echo '<input class="checkbox of-input" type="checkbox" id="' . $meta_box['name'] . '" name="' . $meta_box['name'] . '" value="1" ' . $checked . ' />';
						echo '<label class="explain" for="' . $meta_box['name'] .'">' . $meta_box['description'] . '</label>';
						echo '</div>';
						echo '<div class="clear"></div>';
						echo '</div>';
						break;

					case 'info':
						echo '<h4 class="heading">' . $meta_box['title'] . '</h4>';
						echo '<div class="option">';
						echo '<div class="controls">';
						echo '<p class="explain">' . $meta_box['description'] . '</p>';
						echo '</div>';
						echo '<div class="clear"></div>';
						echo '</div>';
						break;
						
					case 'select':
						echo '<h4 class="heading">' . $meta_box['title'] . '</h4>';
						echo '<div class="option">';
						echo '<div class="controls">';
                        echo '<select id="' . $meta_box['name'] . '" name="' . $meta_box['name'] . '" class="of-input">';
						// Loop through each option in the array
						foreach ( $meta_box['options'] as $option ) {
							if ( is_array( $option ) ) {
								echo '<option ' . ( $meta_box_value == $option['value'] ? 'selected="selected"' : '' ) . ' value="' . $option['value'] . '">' . $option['text'] . '</option>';
							} else {
   								echo '<option ' . ( $meta_box_value == $option ? 'selected="selected"' : '' ) . ' value="' . $option['value'] . '">' . $option['text'] . '</option>';
							}
						}                        
						echo '</select>';
						echo '</div>';
						echo '<div class="explain explain-' . $meta_box['type'] . '">' . $meta_box['description'] . '</div>';
						echo '<div class="clear"></div>';
						echo '</div>';
                        break;

					case 'portfolio_cat':
						echo '<h4 class="heading">' . $meta_box['title'] . '</h4>';
						echo '<div class="option">';
						echo '<div class="controls">';
						echo '<ul class="sort-children">';
						
						// If building the portfolio categories list, bring the already selected and ordered cats to the top					
						$selected_cats = explode( ",", $meta_box_value );
						foreach ( $selected_cats as $selected_cat ) { 
							if ( $selected_cat != ' ' && $selected_cat != '' ) {
								$tax_term = get_term( $selected_cat, 'portfolio_category' );
								$parent = get_term( $tax_term->parent, 'portfolio_category' );
								$crumbs = array();
								$crumbs[] = $tax_term->name;
								while ( $parent != null && ! is_wp_error( $parent ) ) {
									$crumbs[] = $parent->name;
									$parent = get_term( $parent->parent, 'portfolio_category' );
								}
								$crumbs = array_reverse( $crumbs );
								$crumbs_str = '';
								foreach ( $crumbs as $crumb ) $crumbs_str .= ( $crumb . ' - ' );
								$crumbs_str = substr( $crumbs_str, 0, -3 );
								echo '<li class="sortable"><input id="' . $meta_box['name'] . '_' . $selected_cat . '" class="checkbox of-input" type="checkbox" name="' . $meta_box['name'] . '[]" value="' . $selected_cat . '" checked="checked" /><label for="' . $meta_box['name'] . '_' . $selected_cat . '">' . $crumbs_str . '</label></li>';
							}
						}

						$unselected_args = array( 'taxonomy' => 'portfolio_category', 'hide_empty' => '0', 'exclude' => $selected_cats );
						$unselected_cats = get_categories( $unselected_args );
						foreach ( $unselected_cats as $unselected_cat ) { 
							$tax_term = get_term( $unselected_cat, 'portfolio_category' );
							$parent = get_term( $tax_term->parent, 'portfolio_category' );
							$crumbs = array();
							$crumbs[] = $tax_term->name;
							while ( $parent != null && ! is_wp_error($parent) ) {
								$crumbs[] = $parent->name;
								$parent = get_term( $parent->parent, 'portfolio_category' );
							}
							$crumbs = array_reverse( $crumbs );
							$crumbs_str = '';
							foreach ( $crumbs as $crumb ) $crumbs_str .= ( $crumb . ' - ' );
							$crumbs_str = substr( $crumbs_str, 0, -3 );
						    echo '<li class="sortable"><input id="' . $meta_box['name'] . '_' . $unselected_cat->cat_ID . '" class="checkbox of-input" type="checkbox" name="' . $meta_box['name'] . '[]" value="' . $unselected_cat->cat_ID . '" /><label for="' . $meta_box['name'] . '_' . $unselected_cat->cat_ID . '">' . $crumbs_str . '</label></li>';
						} 
													
						echo '</ul>';						
						echo '</div>';
						echo '<div class="explain explain-multicheck">' . $meta_box['description'] . '</div>';
						echo '<div class="clear"></div>';
						echo '</div>';
						break;
					
					case 'blog_cat':
						echo '<h4 class="heading">' . $meta_box['title'] . '</h4>';
						echo '<div class="option">';
						echo '<div class="controls">';
						echo '<ul>';
						
						// If building the blog categories list, bring the already selected and ordered cats to the top					
						$selected_cats = explode( ",", $meta_box_value );
						foreach ( $selected_cats as $selected_cat ) { 
							if ( $selected_cat != ' ' && $selected_cat != '' ) {
								$tax_term = get_term( $selected_cat, 'category' );
								$parent = get_term( $tax_term->parent, 'category' );
								$crumbs = array();
								$crumbs[] = $tax_term->name;
								while ( $parent != null && ! is_wp_error( $parent ) ) {
									$crumbs[] = $parent->name;
									$parent = get_term( $parent->parent, 'category' );
								}
								$crumbs = array_reverse( $crumbs );
								$crumbs_str = '';
								foreach ( $crumbs as $crumb ) $crumbs_str .= ( $crumb . ' - ' );
								$crumbs_str = substr( $crumbs_str, 0, -3 );
		                		echo '<li><input id="' . $meta_box['name'] . '_' . $selected_cat . '" class="checkbox of-input" type="checkbox" name="' . $meta_box['name'] . '[]" value="' . $selected_cat . '" checked="checked" /><label for="' . $meta_box['name'] . '_' . $selected_cat . '">' . $crumbs_str . '</label></li>';
		                	}
						}
						
						$unselected_args = array( 'taxonomy' => 'category', 'hide_empty' => '0', 'exclude' => $selected_cats );
						$unselected_cats = get_categories( $unselected_args );
		                foreach ( $unselected_cats as $unselected_cat ) { 
		                	$tax_term = get_term( $unselected_cat, 'category' );
							$parent = get_term( $tax_term->parent, 'category' );
							$crumbs = array();
							$crumbs[] = $tax_term->name;
							while ( $parent != null && ! is_wp_error( $parent ) ) {
								$crumbs[] = $parent->name;
								$parent = get_term( $parent->parent, 'category' );
							}
							$crumbs = array_reverse( $crumbs );
							$crumbs_str = '';
							foreach ( $crumbs as $crumb ) $crumbs_str .= ( $crumb . ' - ' );
							$crumbs_str = substr( $crumbs_str, 0, -3 );
		                    echo '<li><input id="' . $meta_box['name'] . '_' . $unselected_cat->cat_ID . '" class="checkbox of-input" type="checkbox" name="' . $meta_box['name'] . '[]" value="' . $unselected_cat->cat_ID . '" /><label for="' . $meta_box['name'] . '_' . $unselected_cat->cat_ID . '">' . $crumbs_str . '</label></li>';
		                } 
													
						echo '</ul>';
						echo '</div>';
						echo '<div class="explain explain-multicheck">' . $meta_box['description'] . '</div>';
						echo '<div class="clear"></div>';
						echo '</div>';
						break;
						
					case 'slider_cat':
						echo '<h4 class="heading">' . $meta_box['title'] . '</h4>';
						echo '<div class="option">';
						echo '<div class="controls">';
						echo '<ul>';
						
						// If building the slider categories list, bring the already selected and ordered cats to the top					
						$selected_cats = explode( ",", $meta_box_value );
						foreach ( $selected_cats as $selected_cat ) { 
							if ( $selected_cat != ' ' && $selected_cat != '' ) {
								$tax_term = get_term( $selected_cat, 'slider_category' );
								$parent = get_term( $tax_term->parent, 'slider_category' );
								$crumbs = array();
								$crumbs[] = $tax_term->name;
								while ( $parent != null && ! is_wp_error( $parent ) ) {
									$crumbs[] = $parent->name;
									$parent = get_term( $parent->parent, 'slider_category' );
								}
								$crumbs = array_reverse( $crumbs );
								$crumbs_str = '';
								foreach ( $crumbs as $crumb ) $crumbs_str .= ( $crumb . ' - ' );
								$crumbs_str = substr( $crumbs_str, 0, -3 );
		                		echo '<li><input id="' . $meta_box['name'] . '_' . $selected_cat . '" class="checkbox of-input" type="checkbox" name="' . $meta_box['name'] . '[]" value="' . $selected_cat . '" checked="checked" /><label for="' . $meta_box['name'] . '_' . $selected_cat . '">' . $crumbs_str . '</label></li>';
		                	}
						}
						
						$unselected_args = array( 'taxonomy' => 'slider_category', 'hide_empty' => '0', 'exclude' => $selected_cats );
						$unselected_cats = get_categories( $unselected_args );
		                foreach ( $unselected_cats as $unselected_cat ) { 
		                	$tax_term = get_term( $unselected_cat, 'slider_category' );
							$parent = get_term( $tax_term->parent, 'slider_category' );
							$crumbs = array();
							$crumbs[] = $tax_term->name;
							while ( $parent != null && ! is_wp_error( $parent ) ) {
								$crumbs[] = $parent->name;
								$parent = get_term( $parent->parent, 'slider_category' );
							}
							$crumbs = array_reverse( $crumbs );
							$crumbs_str = '';
							foreach ( $crumbs as $crumb ) $crumbs_str .= ( $crumb . ' - ' );
							$crumbs_str = substr( $crumbs_str, 0, -3 );
		                    echo '<li><input id="' . $meta_box['name'] . '_' . $unselected_cat->cat_ID . '" class="checkbox of-input" type="checkbox" name="' . $meta_box['name'] . '[]" value="' . $unselected_cat->cat_ID . '" /><label for="' . $meta_box['name'] . '_' . $unselected_cat->cat_ID . '">' . $crumbs_str . '</label></li>';
		                } 
													
						echo '</ul>';
						echo '</div>';
						echo '<div class="explain explain-multicheck">' . $meta_box['description'] . '</div>';
						echo '<div class="clear"></div>';
						echo '</div>';
						break;
						
					case 'image':
						echo '<h4 class="heading">' . $meta_box['title'] . '</h4>';
						echo '<div class="option">';
						echo '<div class="controls">';						
						echo optionsframework_uploader( $meta_box['name'], $meta_box_value, null, '' );
						echo '</div>';
						echo '<div class="explain explain-background">' . $meta_box['description'] . '</div>';
						echo '<div class="clear"></div>';
						echo '</div>';
						break;

					case 'background':
						echo '<h4 class="heading">' . $meta_box['title'] . '</h4>';
						echo '<div class="option">';
						echo '<div class="controls">';											

						// Gets the unique option id
						$optionsframework_settings = get_option('optionsframework');
						if ( isset( $optionsframework_settings['id'] ) ) {
							$option_name = $optionsframework_settings['id'];
						}
						else {
							$option_name = 'unipress';
						};

						$background = $meta_box_value;

						if ( ! isset( $background['repeat'] ) ) $background['repeat'] = 'repeat';
						if ( ! isset( $background['position'] ) ) $background['position'] = 'top center';
						if ( ! isset( $background['attachment'] ) ) $background['attachment'] = 'scroll';
						
						// Background Image - New AJAX Uploader using Media Library
						if ( ! isset($background['image'] ) ) {
							$background['image'] = '';
						}
						
						echo optionsframework_uploader( $meta_box['name'], $background['image'], null, esc_attr( $option_name . '[' . $meta_box['name'] . '][image]' ) );
						$class = 'of-background-properties';
						if ( '' == $background['image'] ) {
							$class .= ' hide';
						}
						echo '<div class="' . esc_attr( $class ) . '">';
						
						// Background Repeat
						echo '<select class="of-background of-background-repeat" name="' . esc_attr( $option_name . '[' . $meta_box['name'] . '][repeat]'  ) . '" id="' . esc_attr( $meta_box['name'] . '_repeat' ) . '">';
						$repeats = of_recognized_background_repeat();
						
						foreach ( $repeats as $key => $repeat ) {
							echo '<option value="' . esc_attr( $key ) . '" ' . selected( $background['repeat'], $key, false ) . '>'. esc_html( $repeat ) . '</option>';
						}
						echo '</select>';
						
						// Background Position
						echo '<select class="of-background of-background-position" name="' . esc_attr( $option_name . '[' . $meta_box['name'] . '][position]' ) . '" id="' . esc_attr( $meta_box['name'] . '_position' ) . '">';
						$positions = of_recognized_background_position();
						
						foreach ( $positions as $key=>$position ) {
							echo '<option value="' . esc_attr( $key ) . '" ' . selected( $background['position'], $key, false ) . '>'. esc_html( $position ) . '</option>';
						}
						echo '</select>';
						
						// Background Attachment
						echo '<select class="of-background of-background-attachment" name="' . esc_attr( $option_name . '[' . $meta_box['name'] . '][attachment]' ) . '" id="' . esc_attr( $meta_box['name'] . '_attachment' ) . '">';
						$attachments = of_recognized_background_attachment();
						
						foreach ( $attachments as $key => $attachment ) {
							echo '<option value="' . esc_attr( $key ) . '" ' . selected( $background['attachment'], $key, false ) . '>' . esc_html( $attachment ) . '</option>';
						}
						echo '</select>';
						echo '</div>';
						echo '</div>';
						echo '<div class="explain explain-background">' . $meta_box['description'] . '</div>';
						echo '<div class="clear"></div>';
						echo '</div>';
						break;

					case 'color':
						$default_color = '';
						if ( isset($meta_box['std']) ) {
							if ( $meta_box_value !=  $meta_box['std'] ) {
								$default_color = ' data-default-color="' . $meta_box['std'] . '" ';
							}
						}
						echo '<h4 class="heading">' . $meta_box['title'] . '</h4>';
						echo '<div class="option">';
						echo '<div class="controls">';
						echo '<input name="' . esc_attr( $meta_box['name'] ) . '" id="' . esc_attr( $meta_box['name'] ) . '" class="of-color" type="text" value="' . esc_attr( $meta_box_value ) . '"' . $default_color . ' />';
						echo '</div>';
						echo '<div class="explain explain-color">' . $meta_box['description'] . '</div>';
						echo '<div class="clear"></div>';
						echo '</div>';
						break;

					case 'sidebar':
						echo '<h4 class="heading">' . $meta_box['title'] . '</h4>';
						echo '<div class="option">';
						echo '<div class="controls">';

						$sidebars = of_get_option( 'sidebar_list' );
						if( ! $sidebars )
							$sidebars = array();
						array_unshift( $sidebars, Array( 'id' => 'unisphere_empty', 'name' => __( 'Empty sidebar', 'unipress' ) ) );

						$default_sidebars = Array();
						foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) { 
							$default_sidebars[] = Array( 'id' => $sidebar['id'], 'name' => $sidebar['name'] );
						}

						$meta_box_values = explode( ",", $meta_box_value );

						foreach( $default_sidebars as $default_sidebar ) {
							echo __( "Replace <strong> {$default_sidebar['name']} </strong> with", 'unipress' );
							echo '<select name="' . $meta_box['name'] . '[]">';
							echo '<option value="">' . __( 'None', 'unipress' ) . '</option>';
							foreach ( $sidebars as $sidebar ) {
								$selected = '';
								foreach ($meta_box_values as $meta_box_val) {
									if( $meta_box_val == $default_sidebar['id'] . ';' . $sidebar['id'] ) {
										$selected = 'selected="selected"';
									}
								}
								echo '<option ' . $selected . ' value="' . $default_sidebar['id'] . ';' . $sidebar['id'] . '">' . $sidebar['name'] . '</option>';
							}
							echo '</select>';
							echo '<br />';
						}
						echo '</div>';
						echo '<div class="explain explain-color">' . $meta_box['description'] . '</div>';
						echo '<div class="clear"></div>';
						echo '</div>';
						break;
				}

				echo '</div>';
			}
		}
	}
	
	echo '</div>';
}


/**
 * Saves the data posted from the meta box
 *
 * @since	1.0.0
 */
function unipress_save_postdata( $post_id ) {
	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if ( ! isset( $_POST['unisphere_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['unisphere_meta_box_nonce'], basename( __FILE__ ) ) ) {
		return $post_id;
	}
	
	if ( wp_is_post_revision( $post_id ) or wp_is_post_autosave( $post_id ) )
		return $post_id;
		
	global $post;
	$new_meta_boxes =& _unipress_custom_fields();

	$optionsframework_settings = get_option( 'optionsframework' );

	foreach( $new_meta_boxes as $meta_box ) {

		if ( $meta_box['type'] != 'title' && $meta_box['type'] != 'toggle-start' && $meta_box['type'] != 'toggle-end' ) {
		
			if ( 'page' == $_POST['post_type'] ) {
				if ( ! current_user_can( 'edit_page', $post_id ) )
					return $post_id;
			} else {
				if ( ! current_user_can( 'edit_post', $post_id ) )
					return $post_id;
			}
			
			if ( isset( $_POST[ $meta_box['name'] ] ) && is_array( $_POST[ $meta_box['name'] ] ) ) {
				$cats = '';
				foreach ( $_POST[ $meta_box['name'] ] as $cat){
					$cats .= $cat . ",";
				}
				while( strpos( $cats, ',,' ) !== false )
					$cats = str_replace( ',,', ',', $cats );
					
				if( unipress_ends_with( $cats, ',') )
					$cats = substr($cats, 0, -1);
				
				if( unipress_starts_with( $cats, ',') )
					$cats = substr($cats, 1);

				$data = $cats;
			} elseif ( isset( $_POST[ $optionsframework_settings['id'] ][ $meta_box['name'] ] ) ) { // Upload and background fields (using the optionsframework media uploader)
				$data = ( isset( $_POST[ $optionsframework_settings['id'] ][ $meta_box['name'] ] ) && $_POST[ $optionsframework_settings['id'] ][ $meta_box['name'] ] != '' ? $_POST[ $optionsframework_settings['id'] ][ $meta_box['name'] ] : '' );
			} else { 
				$data = ( isset($_POST[ $meta_box['name'] ] ) && $_POST[ $meta_box['name'] ] != '' ? $_POST[ $meta_box['name'] ] : '' ); 
			}

			if ( get_post_meta( $post_id, $meta_box['name'] ) == "" )
				add_post_meta( $post_id, $meta_box['name'], $data, true );
			elseif ( $data == "" )
				delete_post_meta( $post_id, $meta_box['name'], get_post_meta( $post_id, $meta_box['name'], true ) );
			elseif ( $data != get_post_meta( $post_id, $meta_box['name'], true ) )
				update_post_meta( $post_id, $meta_box['name'], $data );
		}
	}
}
add_action('save_post', 'unipress_save_postdata');


/**
 * Enqueues needed scripts
 *
 * @since	1.0.0
 */
function unisphere_custom_fields_admin_scripts() {
	global $post;
	if(isset($post) && ($post->post_type == 'page' || $post->post_type == 'post' || $post->post_type == 'portfolio_cpt' || $post->post_type == 'slider')) {
		// Enqueue colorpicker scripts for versions below 3.5 for compatibility
		if ( ! wp_script_is( 'wp-color-picker', 'registered' ) ) {
			wp_register_script( 'iris', trailingslashit( UNIPRESS_ADMIN_URI ) . 'js/iris.min.js', array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );
			wp_register_script( 'wp-color-picker', trailingslashit( UNIPRESS_ADMIN_URI ) . 'js/color-picker.min.js', array( 'jquery', 'iris' ) );
			$colorpicker_l10n = array(
				'clear' => __( 'Clear','unipress' ),
				'defaultString' => __( 'Default', 'unipress' ),
				'pick' => __( 'Select Color', 'unipress' )
			);
			wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );
		}

		// Enqueue custom fields JS
		wp_enqueue_script( 'unipress-custom-fields-scripts', trailingslashit( UNIPRESS_ADMIN_JS ) . 'custom-fields.js', array( 'jquery', 'wp-color-picker' ) );  
	}
}
add_action( 'admin_enqueue_scripts', 'unisphere_custom_fields_admin_scripts' );


/**
 * Enqueues needed styles
 *
 * @since	1.0.0
 */
function unisphere_custom_fields_admin_styles() {
	global $post;
	if(isset($post) && ($post->post_type == 'page' || $post->post_type == 'post' || $post->post_type == 'portfolio_cpt' || $post->post_type == 'slider')) {
		wp_enqueue_style( 'unipress-custom-fields-styles', trailingslashit( UNIPRESS_ADMIN_CSS ) . 'custom-fields.css');
			if ( ! wp_style_is( 'wp-color-picker','registered' ) ) {
			wp_register_style( 'wp-color-picker', trailingslashit( UNIPRESS_ADMIN_URI ) . 'css/color-picker.min.css' );
		}
		wp_enqueue_style( 'wp-color-picker' );
	}
}
add_action('admin_print_styles', 'unisphere_custom_fields_admin_styles');


/**
 * Wrapper for unipress_custom_fields()
 *
 * Allows for setting options via a return statement in the
 * custom-fields.php theme file.  For example (in custom-fields.php):
 *
 * <code>
 * return array(...);
 * </code>
 *
 * @return array (by reference)
 * @since	1.0.0
 */
function &_unipress_custom_fields() {
	static $customfields = null;

	if ( !$customfields ) {
		// Load options from custom-fields.php file (if it exists)
		$location = apply_filters( 'unipress_custom_fields_location', array('custom-fields.php') );
		if ( $customfieldsfile = locate_template( $location ) ) {
			$maybe_options = require_once $customfieldsfile;
			if ( is_array($maybe_options) ) {
				$customfields = $maybe_options;
			} else if ( function_exists( 'unipress_custom_fields' ) ) {
				$customfields = unipress_custom_fields();
			}
		}
	}

	$combined_options = array();

	// Append the framework default custom fields
	if( is_array( $customfields ) ) {
		$combined_options = array_merge( $customfields, unipress_get_default_custom_fields() );
	} else {
		$combined_options = unipress_get_default_custom_fields();
	}

	return $combined_options;
}