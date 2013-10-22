/**
 * Prints out the inline javascript needed for the colorpicker and choosing
 * the tabs in the panel.
 */

jQuery(document).ready(function($) {

	// Fade out the save message
	$('.fade').delay(1000).fadeOut(1000);

	$('.of-color.hsl').wrap('<span class="of-color-hsl"></span>');
	$('.of-color').wpColorPicker();

	// Switches option sections
	$('.group').hide();
	var active_tab = '';
	if (typeof(localStorage) != 'undefined' ) {
		active_tab = localStorage.getItem("active_tab");
	}
	if (active_tab != '' && $(active_tab).length ) {
		$(active_tab).fadeIn();
	} else {
		$('.group:first').fadeIn();
	}
	$('.group .collapsed').each(function(){
		$(this).find('input:checked').parent().parent().parent().nextAll().each(
			function(){
				if ($(this).hasClass('last')) {
					$(this).removeClass('hidden');
						return false;
					}
				$(this).filter('.hidden').removeClass('hidden');
			});
	});
	if (active_tab != '' && $(active_tab + '-tab').length ) {
		$(active_tab + '-tab').addClass('nav-tab-active');
	}
	else {
		$('.nav-tab-wrapper a:first').addClass('nav-tab-active');
	}

	$('.nav-tab-wrapper a').click(function(evt) {
		$('.nav-tab-wrapper a').removeClass('nav-tab-active');
		$(this).addClass('nav-tab-active').blur();
		var clicked_group = $(this).attr('href');
		if (typeof(localStorage) != 'undefined' ) {
			localStorage.setItem("active_tab", $(this).attr('href'));
		}
		$('.group').hide();
		$(clicked_group).fadeIn();
		evt.preventDefault();

		// Editor Height (needs improvement)
		$('.wp-editor-wrap').each(function() {
			var editor_iframe = $(this).find('iframe');
			if ( editor_iframe.height() < 30 ) {
				editor_iframe.css({'height':'auto'});
			}
		});

	});

	$('.group .collapsed input:checkbox').click(unhideHidden);

	function unhideHidden(){
		if ($(this).attr('checked')) {
			$(this).parent().parent().parent().nextAll().removeClass('hidden');
		}
		else {
			$(this).parent().parent().parent().nextAll().each(
			function(){
				if ($(this).filter('.last').length) {
					$(this).addClass('hidden');
					return false;
					}
				$(this).addClass('hidden');
			});

		}
	}

	// Image Options
	$('.of-radio-img-img').click(function(){
		$(this).parent().parent().find('.of-radio-img-img').removeClass('of-radio-img-selected');
		$(this).addClass('of-radio-img-selected');
	});

	$('.of-radio-img-label').hide();
	$('.of-radio-img-img').show();
	$('.of-radio-img-radio').hide();

	/* Toggle */
	$(".toggle-container .toggle-content").hide(); //Hide (Collapse) the toggle containers on load
	$(".toggle-container .toggle-sign").text('+'); //Add the + sign on load

	$(".toggle-container .toggle").click(function(e) {
		if(!jQuery(this).hasClass('selected')) {
			$(this).addClass('selected');
			$(this).find('.toggle-sign').text('-');
			$(this).next(".toggle-content").slideToggle();
		} else {
			$(this).removeClass('selected');
			$(this).find('.toggle-sign').text('+');
			$(this).next(".toggle-content").slideToggle();
		}
	});

	// Add confirmation dialog when deleting a sidebar
	var sidebar_delete_submitted = false;
	$('#section-sidebar_list .remove-button').click(function(e) {
		if(!sidebar_delete_submitted && !confirm($(this).attr('data-confirm-message'))) 	{
		    e.preventDefault();
		} else {
			sidebar_delete_submitted = true;
			$(this).click();
		}
	});

	// Init the jQuery UI sliders for the HSL (hue, saturation, lightness)
	if( $("#slider-h").length > 0 ) {
		$("#slider-h").slider({
			orientation: "vertical",
			range: "min",
			min: -180,
			max: 180,
			value: 0,
			step: 1,
			slide: refreshColors
		});

		$("#slider-s, #slider-l").slider({
			orientation: "vertical",
			range: "min",
			min: -100,
			max: 100,
			value: 0,
			step: 1,
			slide: refreshColors
		});

		var orig_colors = new Array();
		$('.of-color.hsl').each(function() {
			orig_colors[$(this).attr('id')] = $(this).val();
		});
	}

	/**
	 * Converts an RGB color value to HSL. Conversion formula
	 * adapted from http://en.wikipedia.org/wiki/HSL_color_space.
	 * Assumes r, g, and b are contained in the set [0, 255] and
	 * returns h, s, and l in the set [0, 1].
	 *
	 * @param   Number  r       The red color value
	 * @param   Number  g       The green color value
	 * @param   Number  b       The blue color value
	 * @return  Array           The HSL representation
	 */
	function rgbToHsl(r, g, b){
		r /= 255, g /= 255, b /= 255;
		var max = Math.max(r, g, b), min = Math.min(r, g, b);
		var h, s, l = (max + min) / 2;

		if(max == min){
			h = s = 0; // achromatic
		} else {
			var d = max - min;
			s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
			switch(max){
				case r: h = (g - b) / d + (g < b ? 6 : 0); break;
				case g: h = (b - r) / d + 2; break;
				case b: h = (r - g) / d + 4; break;
			}
			h /= 6;
		}

		return [h, s, l];
	}

	// Converts an hex value to and hsl value
	function hexToHsl(hex) {
		var r = parseInt(hex.substr(1,2), 16);
		var g = parseInt(hex.substr(3,2), 16);
		var b = parseInt(hex.substr(5,2), 16);

		return rgbToHsl(r, g, b);
	}

	/**
	 * Converts an HSL color value to RGB. Conversion formula
	 * adapted from http://en.wikipedia.org/wiki/HSL_color_space.
	 * Assumes h, s, and l are contained in the set [0, 1] and
	 * returns r, g, and b in the set [0, 255].
	 *
	 * @param   Number  h       The hue
	 * @param   Number  s       The saturation
	 * @param   Number  l       The lightness
	 * @return  Array           The RGB representation
	 */
	function hslToRgb(h, s, l){
		var r, g, b;

		if(s == 0){
			r = g = b = l; // achromatic
		}else{
			function hue2rgb(p, q, t){
				if(t < 0) t += 1;
				if(t > 1) t -= 1;
				if(t < 1/6) return p + (q - p) * 6 * t;
				if(t < 1/2) return q;
				if(t < 2/3) return p + (q - p) * (2/3 - t) * 6;
				return p;
			}

			var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
			var p = 2 * l - q;
			r = hue2rgb(p, q, h + 1/3);
			g = hue2rgb(p, q, h);
			b = hue2rgb(p, q, h - 1/3);
		}

		return [r * 255, g * 255, b * 255];
	}

	function componentToHex(c) {
		var hex = c.toString(16);
		hex = hex.replace('-', '');
		return hex.length == 1 ? "0" + hex : hex;
	}

	function hslToHex(h, s, l) {
		var hsl = hslToRgb(h, s, l);
		return '#' + componentToHex(parseInt(hsl[0])) + componentToHex(parseInt(hsl[1])) + componentToHex(parseInt(hsl[2]));
	}

	// Refreshes the color pickers color when the HSL sliders are changed
	function refreshColors() {
		$('.of-color.hsl').each(function() {
			if( $('#'+$(this).attr('id')+'_enable_hsl').is(':checked') ) {
				new_color = transformColor(orig_colors[$(this).attr('id')]);
				$('#'+$(this).attr('id')).parent().prev().css('background-color', new_color);
				$('#'+$(this).attr('id')).val(new_color);
			}
		});
	}

	// Transforms the original colors based on the HSL value of the sliders
	function transformColor(orig_color) {
		var orig_color_hsl = hexToHsl(orig_color);

		// Hue
		var h = parseFloat(orig_color_hsl[0])*360;
		h += parseInt($("#slider-h").slider("value"));
		if(h > 360) h -= 360;

		// Saturation
		var s = parseFloat(orig_color_hsl[1])*100;
		var added_s = parseInt($("#slider-s").slider("value"));

		if( added_s > 0 )
			s += ( (100 - s) * added_s / 100 );
		else
			s += ( s * added_s / 100 );

		if(s > 100) s = 100;
		if(s < 0) s = 0;

		// Lightness
		var l = parseFloat(orig_color_hsl[2])*100;
		var added_l = parseInt($("#slider-l").slider("value"));

		if( added_l > 0 )
			l += ( (100 - l) * added_l / 100 );
		else
			l += ( l * added_l / 100 );

		if(l > 100) l = 100;
		if(l < 0) l = 0;

		return hslToHex(h/360, s/100, l/100);
	}
});