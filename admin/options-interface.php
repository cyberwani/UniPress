<?php

/**
 * Generates the tabs that are used in the options menu
 */

function optionsframework_tabs() {
	$counter = 0;
	$options =& _optionsframework_options();
	$menu = '';

	foreach ( $options as $value ) {
		// If the current location doesn't match the heading continue to next iteration
		if ( ! isset( $value['location'] ) || $value['location'] != optionsframework_get_current_location() ) {
			continue;
		}

		// Heading for Navigation
		if ( $value['type'] == "heading" && ! empty( $value['name'] ) ) {
			$counter++;
			$class = '';
			$class = ! empty( $value['id'] ) ? $value['id'] : $value['name'];
			$class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($class) ) . '-tab';
			$menu .= '<a id="options-group-' . $counter . '-tab" class="nav-tab ' . $class .'" title="' . esc_attr( $value['name'] ) . '" href="' . esc_attr( '#options-group-' . $counter ) . '">' . esc_html( $value['name'] ) . '</a>';
		}
	}

	return $menu;
}

/**
 * Generates the options fields that are used in the form.
 */

function optionsframework_fields() {

	global $allowedtags;
	$optionsframework_settings = get_option( 'optionsframework' );

	// Gets the unique option id
	if ( isset( $optionsframework_settings['id'] ) ) {
		$option_name = $optionsframework_settings['id'];
	}
	else {
		$option_name = 'unipress';
	};

	$settings = get_option($option_name);
	$options =& _optionsframework_options();

	$counter = 0;
	$menu = '';

	foreach ( $options as $value ) {

		// If the current location doesn't match the option continue to next iteration
		if ( ! isset( $value['location'] ) || $value['location'] != optionsframework_get_current_location() ) {
			continue;
		}

		$val = '';
		$select_value = '';
		$output = '';

		// Wrap all options
		if ( ( $value['type'] != "heading" ) && ( $value['type'] != "info" ) && ( $value['type'] != "toggle-start" ) && ( $value['type'] != "toggle-end" ) ) {

			// Keep all ids lowercase with no spaces
			$value['id'] = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower( $value['id'] ) );

			$id = 'section-' . $value['id'];

			$class = 'section';
			if ( isset( $value['type'] ) ) {
				$class .= ' section-' . $value['type'];
			}
			if ( isset( $value['class'] ) ) {
				$class .= ' ' . $value['class'];
			}
			if ( isset( $value['hsl'] ) && true == $value['hsl'] ) {
				$class .= ' hsl';
			}

			$output .= '<div id="' . esc_attr( $id ) .'" class="' . esc_attr( $class ) . '">'."\n";
			if ( isset( $value['name'] ) ) {
				$output .= '<h4 class="heading">' . esc_html( $value['name'] ) . '</h4>' . "\n";
			}
			if ( $value['type'] != 'editor' ) {
				$output .= '<div class="option">' . "\n" . '<div class="controls">' . "\n";
			}
			else {
				$output .= '<div class="option">' . "\n" . '<div>' . "\n";
			}
		}

		// Set default value to $val
		if ( isset( $value['std'] ) ) {
			$val = $value['std'];
		}

		// If the option is already saved, override $val
		if ( ( $value['type'] != 'heading' ) && ( $value['type'] != 'info') && ( $value['type'] != "toggle-start" ) && ( $value['type'] != "toggle-end" ) ) {
			if ( isset( $settings[($value['id'])]) ) {
				$val = $settings[($value['id'])];
				// Striping slashes of non-array options
				if ( !is_array($val) ) {
					$val = stripslashes( $val );
				}
			}
		}

		// If there is a description save it for labels
		$explain_value = '';
		if ( isset( $value['desc'] ) ) {
			$explain_value = $value['desc'];
		}

		switch ( $value['type'] ) {

		// Basic text input
		case 'text':
			$output .= '<input id="' . esc_attr( $value['id'] ) . '" class="of-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" type="text" value="' . esc_attr( $val ) . '" />';
			break;

		// Password input
		case 'password':
			$output .= '<input id="' . esc_attr( $value['id'] ) . '" class="of-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" type="password" value="' . esc_attr( $val ) . '" />';
			break;

		// Textarea
		case 'textarea':
			$rows = '8';

			if ( isset( $value['settings']['rows'] ) ) {
				$custom_rows = $value['settings']['rows'];
				if ( is_numeric( $custom_rows ) ) {
					$rows = $custom_rows;
				}
			}

			$val = stripslashes( $val );

			// If it's the unipress default export settings field, then add the base64 encoded string as the value
			if ( 'export_settings' == $value['id'] ) {
				$val = base64_encode( serialize( $settings ) );
			}

			$output .= '<textarea id="' . esc_attr( $value['id'] ) . '" class="of-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" rows="' . $rows . '">' . esc_textarea( $val ) . '</textarea>';
			break;

		// Select Box
		case 'select':
			$output .= '<select class="of-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" id="' . esc_attr( $value['id'] ) . '">';

			foreach ($value['options'] as $key => $option ) {
				$output .= '<option'. selected( $val, $key, false ) .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
			}
			$output .= '</select>';
			break;


		// Radio Box
		case "radio":
			$name = $option_name .'['. $value['id'] .']';
			foreach ($value['options'] as $key => $option) {
				$id = $option_name . '-' . $value['id'] .'-'. $key;
				$output .= '<input class="of-input of-radio" type="radio" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="'. esc_attr( $key ) . '" '. checked( $val, $key, false) .' /><label for="' . esc_attr( $id ) . '">' . esc_html( $option ) . '</label>';
			}
			break;

		// Image Selectors
		case "images":
			$name = $option_name .'['. $value['id'] .']';
			foreach ( $value['options'] as $key => $option ) {
				$selected = '';
				if ( $val != '' && ($val == $key) ) {
					$selected = ' of-radio-img-selected';
				}
				$output .= '<input type="radio" id="' . esc_attr( $value['id'] .'_'. $key) . '" class="of-radio-img-radio" value="' . esc_attr( $key ) . '" name="' . esc_attr( $name ) . '" '. checked( $val, $key, false ) .' />';
				$output .= '<div class="of-radio-img-label">' . esc_html( $key ) . '</div>';
				$output .= '<img src="' . esc_url( $option ) . '" alt="' . $option .'" class="of-radio-img-img' . $selected .'" onclick="document.getElementById(\''. esc_attr($value['id'] .'_'. $key) .'\').checked=true;" />';
			}
			break;

		// Checkbox
		case "checkbox":
			$output .= '<input id="' . esc_attr( $value['id'] ) . '" class="checkbox of-input" type="checkbox" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" '. checked( $val, 1, false) .' />';
			$output .= '<label class="explain" for="' . esc_attr( $value['id'] ) . '">' . wp_kses( $explain_value, $allowedtags) . '</label>';
			break;

		// Multicheck
		case "multicheck":
			foreach ($value['options'] as $key => $option) {
				$checked = '';
				$label = $option;
				$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($key));

				$id = $option_name . '-' . $value['id'] . '-'. $option;
				$name = $option_name . '[' . $value['id'] . '][' . $option .']';

				if ( isset($val[$option]) ) {
					$checked = checked($val[$option], 1, false);
				}

				$output .= '<input id="' . esc_attr( $id ) . '" class="checkbox of-input" type="checkbox" name="' . esc_attr( $name ) . '" ' . $checked . ' /><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
			}
			break;

		// Color picker
		case "color":
			$default_color = '';
			if ( isset($value['std']) ) {
				if ( $val !=  $value['std'] )
					$default_color = ' data-default-color="' . $value['std'] . '" ';
			}
			$class = '';
			// Include "hsl" class if enabled
			if ( isset( $value['hsl'] ) && true == $value['hsl'] ) {
				$class = ' hsl';
			}
			$output .= '<input name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" id="' . esc_attr( $value['id'] ) . '" class="of-color' . $class . '"  type="text" value="' . esc_attr( $val ) . '"' . $default_color .' />';
			// Include "hsl" checkbox if enabled
			if ( isset( $value['hsl'] ) && true == $value['hsl'] ) {
				$output .= '<input type="checkbox" class="of-color-checkbox" name="' . esc_attr( $option_name . '[' . $value['id'] . '_enable_hsl]' ) . '" id="' . esc_attr( $value['id'] . '_enable_hsl' ) . '" checked="checked" />';
			}

			break;

		// Uploader
		case "upload":
			$output .= optionsframework_uploader( $value['id'], $val, null );

			break;

		// Typography
		case 'typography':

			unset( $font_size, $font_lineheight, $font_style, $font_face, $font_uppercase, $font_color );

			$typography_defaults = array(
				'size' => '',
				'lineheight' => '',
				'face' => '',
				'style' => '',
				'uppercase' => '',
				'color' => ''
			);

			$typography_stored = wp_parse_args( $val, $typography_defaults );

			$typography_options = array(
				'sizes' => isset( $value['size'] ) && $value['size'] ? of_recognized_font_sizes() : false,
				'lineheights' => isset( $value['lineheight'] ) && $value['lineheight'] ? of_recognized_font_lineheights() : false,
				'faces' => of_recognized_font_faces(),
				'styles' => isset( $value['style'] ) && $value['style'] ? of_recognized_font_styles() : false,
				'uppercase' => isset( $value['uppercase'] ) && $value['uppercase'] ? true : false,
				'color' => isset( $value['color'] ) && $value['color'] ? true : false
			);

			if ( isset( $value['options'] ) ) {
				$typography_options = wp_parse_args( $value['options'], $typography_options );
			}

			// Font Face
			if ( $typography_options['faces'] ) {
				if ( ! isset( $value['googlefonts'] ) ) {
					$value['googlefonts'] = false;
				}

				$font_face = '<select class="of-typography of-typography-face" name="' . esc_attr( $option_name . '[' . $value['id'] . '][face]' ) . '" id="' . esc_attr( $value['id'] . '_face' ) . '">';

				// Include Google Web Fonts
				if ( true == $value['googlefonts'] ) {
					$font_face .= '<optgroup label="' . __( 'Default font family stacks', 'unipress' ) . '">';
				}

				$faces = $typography_options['faces'];
				$google_optgroup = false;
				foreach ( $faces as $key => $face ) {
					// Google Web Font faces start with "google:"
					if ( true == $value['googlefonts'] && unipress_starts_with( $key, 'google:' ) && false == $google_optgroup ) {
						$google_optgroup = true;
						$font_face .= '</optgroup>';
						$font_face .= '<optgroup label="' . __( 'Google web fonts', 'unipress' ) . '">';
					}

					// Don't display Google Web Fonts if disabled for this field
					if ( ( false == $value['googlefonts'] && ! unipress_starts_with( $key, 'google:' ) ) || true == $value['googlefonts'] ) {
						$font_face .= '<option value="' . esc_attr( $key ) . '" ' . selected( $typography_stored['face'], $key, false ) . '>' . esc_html( $face ) . '</option>';
					}
				}

				// Close the Google Web Fonts optgroup
				if ( true == $value['googlefonts'] ) {
					$font_face .= '</optgroup>';
				}
				$font_face .= '</select>';
			}

			// Font Size
			if ( $typography_options['sizes'] ) {
				$font_size = '<label class="typography-size">' . __( 'Size:', 'unipress' ) . '</label><select class="of-typography of-typography-size" name="' . esc_attr( $option_name . '[' . $value['id'] . '][size]' ) . '" id="' . esc_attr( $value['id'] . '_size' ) . '">';
				$sizes = $typography_options['sizes'];
				foreach ( $sizes as $i ) {
					$size = $i . 'px';
					$font_size .= '<option value="' . esc_attr( $size ) . '" ' . selected( $typography_stored['size'], $size, false ) . '>' . esc_html( $size ) . '</option>';
				}
				$font_size .= '</select>';
			}

			// Font Line Heights
			if ( $typography_options['lineheights'] ) {
				$font_lineheight = '<label class="typography-lineheight">' . __( 'Line height:', 'unipress' ) . '</label><select class="of-typography of-typography-lineheight" name="' . esc_attr( $option_name . '[' . $value['id'] . '][lineheight]' ) . '" id="' . esc_attr( $value['id'] . '_lineheight' ) . '">';
				$lineheights = $typography_options['lineheights'];
				foreach ( $lineheights as $i ) {
					$lineheight = $i . 'px';
					$font_lineheight .= '<option value="' . esc_attr( $lineheight ) . '" ' . selected( $typography_stored['lineheight'], $lineheight, false ) . '>' . esc_html( $lineheight ) . '</option>';
				}
				$font_lineheight .= '</select>';
			}

			// Font Styles
			if ( $typography_options['styles'] ) {
				$font_style = '<label class="typography-style">' . __( 'Font style:', 'unipress' ) . '</label><select class="of-typography of-typography-style" name="'.$option_name.'['.$value['id'].'][style]" id="'. $value['id'].'_style">';
				$styles = $typography_options['styles'];
				foreach ( $styles as $key => $style ) {
					$font_style .= '<option value="' . esc_attr( $key ) . '" ' . selected( $typography_stored['style'], $key, false ) . '>'. $style .'</option>';
				}
				$font_style .= '</select>';
			}

			// Font Color
			if ( $typography_options['color'] ) {
				$default_color = '';
				if ( isset( $value['std']['color'] ) ) {
					if ( $val !=  $value['std']['color'] )
						$default_color = ' data-default-color="' .$value['std']['color'] . '" ';
				}
				$font_color = '<input name="' . esc_attr( $option_name . '[' . $value['id'] . '][color]' ) . '" id="' . esc_attr( $value['id'] . '_color' ) . '" class="of-color of-typography-color  type="text" value="' . esc_attr( $typography_stored['color'] ) . '"' . $default_color .' />';
			}

			// Font Uppercase
			if ( $typography_options['uppercase'] ) {
				$font_uppercase = '<label class="typography-uppercase">Uppercase:</label>';
				$font_uppercase .= '<input id="' . esc_attr( $value['id'] . '_uppercase' ) . '" class="of-typography of-typography-uppercase" type="checkbox" name="' . esc_attr( $option_name . '[' . $value['id'] . '][uppercase]' ) . '" '. checked( $typography_stored['uppercase'], 1, false) .' />';
			}

			// Allow modification/injection of typography fields
			$typography_fields = compact( 'font_face', 'font_size', 'font_lineheight', 'font_style', 'font_uppercase', 'font_color' );
			$typography_fields = apply_filters( 'of_typography_fields', $typography_fields, $typography_stored, $option_name, $value );
			$output .= implode( '', $typography_fields );

			break;

		// Background
		case 'background':

			$background = $val;

			// Background Image
			if (!isset($background['image'])) {
				$background['image'] = '';
			}

			$output .= optionsframework_uploader( $value['id'], $background['image'], null, esc_attr( $option_name . '[' . $value['id'] . '][image]' ) );

			$class = 'of-background-properties';
			if ( '' == $background['image'] ) {
				$class .= ' hide';
			}
			$output .= '<div class="' . esc_attr( $class ) . '">';

			// Background Repeat
			$output .= '<select class="of-background of-background-repeat" name="' . esc_attr( $option_name . '[' . $value['id'] . '][repeat]'  ) . '" id="' . esc_attr( $value['id'] . '_repeat' ) . '">';
			$repeats = of_recognized_background_repeat();

			foreach ($repeats as $key => $repeat) {
				$output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['repeat'], $key, false ) . '>'. esc_html( $repeat ) . '</option>';
			}
			$output .= '</select>';

			// Background Position
			$output .= '<select class="of-background of-background-position" name="' . esc_attr( $option_name . '[' . $value['id'] . '][position]' ) . '" id="' . esc_attr( $value['id'] . '_position' ) . '">';
			$positions = of_recognized_background_position();

			foreach ($positions as $key=>$position) {
				$output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['position'], $key, false ) . '>'. esc_html( $position ) . '</option>';
			}
			$output .= '</select>';

			// Background Attachment
			$output .= '<select class="of-background of-background-attachment" name="' . esc_attr( $option_name . '[' . $value['id'] . '][attachment]' ) . '" id="' . esc_attr( $value['id'] . '_attachment' ) . '">';
			$attachments = of_recognized_background_attachment();

			foreach ($attachments as $key => $attachment) {
				$output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['attachment'], $key, false ) . '>' . esc_html( $attachment ) . '</option>';
			}
			$output .= '</select>';
			$output .= '</div>';

			break;

		// Editor
		case 'editor':
			$output .= '<div class="explain">' . wp_kses( $explain_value, $allowedtags ) . '</div>'."\n";
			echo $output;
			$textarea_name = esc_attr( $option_name . '[' . $value['id'] . ']' );
			$default_editor_settings = array(
				'textarea_name' => $textarea_name,
				'media_buttons' => false,
				'tinymce' => array( 'plugins' => 'wordpress' )
			);
			$editor_settings = array();
			if ( isset( $value['settings'] ) ) {
				$editor_settings = $value['settings'];
			}
			$editor_settings = array_merge( $default_editor_settings, $editor_settings );
			wp_editor( $val, $value['id'], $editor_settings );
			$output = '';
			break;

		// toggle start
		case "toggle-start":
			$output .= '<div class="toggle-container">' . "\n";
			$output .= '<a class="toggle" href="javascript:void(0);"><span class="toggle-sign">+</span><span class="toggle-title">' . esc_html( $value['name'] ) . '</span></a>' . "\n";
			$output .= '<div class="toggle-content" style="display: none;">' . "\n";
			break;

		// toggle end
		case "toggle-end":
			$output .= '</div></div>' . "\n";
			break;

		// Create sidebar
		case "sidebar_create":
			$output .= '<input id="' . esc_attr( $value['id'] ) . '_text" class="of-input" name="' . esc_attr( $option_name . '[' . $value['id'] . '_text]' ) . '" type="text" value="' . esc_attr( $val ) . '" />';
			$output .= '<input type="submit" class="button2" name="sidebar_create" value="' . __( 'Create', 'unipress' ) . '" />';
			break;

		// List sidebars
		case "sidebar_list":
			$i = 0;
			if( !empty( $settings['sidebar_list'] ) ) {
				$output .= '<table class="wp-list-table widefat fixed">';
				$output .= '<thead>';
				$output .= '<tr><th>' . __( 'Name', 'unipress' ) . '</th><th>' . __( 'CSS Class', 'unipress' ) . '</th><th class="manage-column column-cb check-column">&nbsp;</th></tr>';
				$output .= '</thead>';
				$output .= '<tbody>';
				foreach ($settings['sidebar_list'] as $item) {
					foreach ($item as $key => $option) {
						if( $key == 'name' ) {
							$name_sidebar = esc_attr( $option );
							$name_hidden_field = '<input name="' . esc_attr( $option_name . '[sidebar_list][' . $i . '][name]' ) . '" type="hidden" value="' . esc_attr( $option ) . '" />';
						}

						if( $key == 'id' ) {
							$id_sidebar = esc_attr( $option );
							$id_hidden_field = '<input name="' . esc_attr( $option_name . '[sidebar_list][' . $i . '][id]' ) . '" type="hidden" value="' . esc_attr( $option ) . '" />';
						}
					}
					$output .= '<tr class="' . ($i % 2 == 0 ? 'alternate' : '') . '"><td>' . $name_sidebar . $id_hidden_field . '</td><td>sidebar-' . $id_sidebar . $name_hidden_field . '</td><td><input class="remove-button" data-confirm-message="' . __( 'Are you sure you want to delete this sidebar?', 'unipress' ) . '" type="submit" name="sidebar_delete" value="' . $id_sidebar . '" /></td></tr>';
					$i++;
				}
				$output .= '</tbody>';
				$output .= '</table>';
			} else {
				$output .= '<p>' . __( 'No custom sidebars have been created yet.', 'unipress' ) . '</p>';
			}
			break;

		// Google Fonts update
		case "fonts_update_google":
			$output .= '<input type="submit" class="button2" name="fonts_update_google" value="' . __( 'Update', 'unipress' ) . '" />';
			break;

		// Info
		case "info":
			$id = '';
			$class = 'section';
			if ( isset( $value['id'] ) ) {
				$id = 'id="' . esc_attr( $value['id'] ) . '" ';
			}
			if ( isset( $value['type'] ) ) {
				$class .= ' section-' . $value['type'];
			}
			if ( isset( $value['class'] ) ) {
				$class .= ' ' . $value['class'];
			}

			$output .= '<div ' . $id . 'class="' . esc_attr( $class ) . '">' . "\n";
			if ( isset($value['name']) ) {
				$output .= '<h4 class="heading">' . esc_html( $value['name'] ) . '</h4>' . "\n";
			}
			if ( $value['desc'] ) {
				$output .= apply_filters('of_sanitize_info', $value['desc'] ) . "\n";
			}
			$output .= '</div>' . "\n";
			break;

		// Heading for Navigation
		case "heading":
			$counter++;
			if ($counter >= 2) {
				$output .= '</div>'."\n";
			}
			$class = '';
			$class = ! empty( $value['id'] ) ? $value['id'] : $value['name'];
			$class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($class) );
			$output .= '<div id="options-group-' . $counter . '" class="group ' . $class . '">';
			$output .= '<h3>' . esc_html( $value['name'] ) . '</h3>' . "\n";
			
			// If "hsl" is enabled for this option group then add the sliders
			if ( isset( $value['hsl'] ) && true == $value['hsl'] ) {
				$output .= '<div id="hsl-wrapper">';
				$output .= '<span>H&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;S&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;L</span>';
				$output .= '<div id="slider-h"></div>';
				$output .= '<div id="slider-s"></div>';
				$output .= '<div id="slider-l"></div>';
				$output .= '</div>';
			}
			break;
		}

		if ( ( $value['type'] != "heading" ) && ( $value['type'] != "info" ) && ( $value['type'] != "toggle-start" ) && ( $value['type'] != "toggle-end" ) ) {
			$output .= '</div>';
			if ( ( $value['type'] != "checkbox" ) && ( $value['type'] != "editor" ) ) {
				$output .= '<div class="explain">' . wp_kses( $explain_value, $allowedtags ) . '</div>'."\n";
			}
			$output .= '</div></div>'."\n";
		}

		echo $output;
	}
	echo '</div>';
}