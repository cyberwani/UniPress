/* Custom Fields Admin Scripts */

jQuery(document).ready(function($) {

	$('.of-color').wpColorPicker();

	// Toggle
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

	// Custom field parent container margin and padding adjust
	if( $('#custom-fields-form-wrap') )
		$('#custom-fields-form-wrap').parent().css('margin', '0').css('padding', '0');

	// Custom fields multi-checkbox sorting
	if( $.isFunction($.fn.sortable) ) {
		$('.sort-children').sortable();
		$('.sort-children').disableSelection();
	}
});