jQuery(document).ready( function() {
	var term = jQuery('meta[name="term"]').attr('content');
	var url = '';
	if (term) {
		url = '/showusers/'+term;
	} else {
		url = '/showusers';
	}
	if (jQuery('#showusers').length > 0) {
		jQuery.get(url, function(data) {			
			jQuery('#showusers').html(data);
			
			var leftoffset = -23;
			
			if (jQuery('#showusers').position().left == 0  && !jQuery('#showusers').hasClass('full')) {
				jQuery('#showusers img').css('width', 35);
				jQuery('#showusers img').css('height', 35);
				leftoffset = -35;
			} else {
				jQuery('#showusers').css('float', 'right');
			}
			
			if (jQuery('#showusers').position().top > 0 && !jQuery('#showusers').hasClass('full')) {
				jQuery('#showusers').css('float', 'left');
			}
							
			jQuery('#showusers img[title]').tooltip({effect: 'toggle', position: 'top right', offset: [-1, leftoffset],
		    events: {
				def:     "mouseover,mouseout",
		        input:   "",
		        widget:  "",
		        tooltip: ""
				}
	  	});
	  	
		});
	}
		
});