Drupal.behaviors.thechurch = {
	attach: function(context, settings) {

		// Add body classes basead on Browser and Operating System
		jQuery('body').addClass(jQuery.browser.name).addClass(jQuery.browser.className).addClass(jQuery.layout.name).addClass(jQuery.layout.className).addClass(jQuery.os.name);
		
		// If the height of the title exceedes that of what it should, change it to a block to force everything to the next line.
		if (jQuery('.title').height() > 33) {
			jQuery('.citytitle').css('display', 'block');
		}
		
		// Auto Resize the Textarea
		jQuery('#post-node-form #edit-body-und-0-value').autoResize({
		  extraSpace : 13,
		  animate : false
		});
		jQuery('#edit-comment-body-und-0-value').autoResize({
		  extraSpace : 13,
		  animate : false
		});
	
		// Modify the date based on the local of user.
		jQuery('.date span').each(function() {
			var date = new Date(jQuery(this).attr('content'));
			var year = date.getFullYear();
			var month = date.getMonth();
			var day = date.getDate();
			var hours = date.getHours();
			if (!isNaN(year)) {
				var period = (hours < 13) ? 'am' : 'pm';
				var minutes = date.getMinutes();
				var fulldate = String(year)+String(month)+String(day);
				var current = new Date();
				var fullcurrent = String(current.getFullYear())+String(current.getMonth())+String(current.getDate());
				var today = (fulldate == fullcurrent) ? true : false;
				month = month+1;
				hours = (hours+1 < 13) ? hours : hours-12;
				minutes = (minutes < 10) ? '0'+minutes : minutes;
				if (today) {
					jQuery(this).text(hours+':'+minutes+period);
				} else {
					jQuery(this).text(month+'/'+day+'/'+year+' '+hours+':'+minutes+period);
				}
			}
		});
		
		// When an ajax form is submited, give the user some indication of this by adding a class to the form
		jQuery('#comment-form .form-submit.ajax-processed').mousedown(function() {
			jQuery('#comment-form').addClass('progress');
		});
  
  }
}
