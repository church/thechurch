Drupal.behaviors.thechurch = {
	attach: function(context, settings) {
	
		// Add body classes basead on Browser and Operating System
jQuery('body').once().addClass(jQuery.browser.name).addClass(jQuery.browser.className).addClass(jQuery.layout.name).addClass(jQuery.layout.className).addClass(jQuery.os.name);
		
		// If the height of the title exceedes that of what it should, change it to a block to force everything to the next line.
		if (jQuery('.title').height() > 33) {
			jQuery('.citytitle').once().css('display', 'block');
		}
		
		
		// Setup an Ajax Link
		jQuery('a.ajax-link:not(.ajax-processed)').addClass('ajax-processed').each(function() {
			
			// Cret the element settings object
			var element_settings = {};
			
			// Get rid of the progress
			element_settings.progress = { 'type' : 'none' };
			
			// setup the click elements and add the href
			if (jQuery(this).attr('href')) {
				element_settings.url = jQuery(this).attr('href');
				element_settings.event = 'click';
			}
			
			element_settings.effect = 'fade';
								
			// Get the base
			var base = jQuery(this).attr('id');
						
			// Register the Ajax Request with Drupal
			Drupal.ajax[base] = new Drupal.ajax(base, this, element_settings);
					
		});				
		
		jQuery('.comment-wrapper .item-list ul.pager').once(function () {
		
			var list = jQuery(this);
			var html = list.html();
			
			jQuery('.comment-wrapper .item-list').remove();
			
			if (jQuery(list).children('li').hasClass('pager-previous')) {
				jQuery('.comment-list').prepend('<div class="item-list"><ul class="pager-previous">'+html+'</ul></div>');
			} 
			if (jQuery(list).children('li').hasClass('pager-next')) {
				jQuery('.comment-list').append('<div class="item-list"><ul class="pager-next">'+html+'</ul></div>');
			}
			
			jQuery('ul.pager-previous li.pager-next').remove();
			
			jQuery('ul.pager-next li.pager-previous').remove();
			
		});
				
		// Setup Ajax Paging on Comments
		jQuery('.comment-wrapper ul.pager li a:not(.ajax-processed), .comment-wrapper ul.pager-next li a:not(.ajax-processed), .comment-wrapper ul.pager-previous li a:not(.ajax-processed)').addClass('ajax-processed').each(function() {
			
			jQuery(this).attr('title', '');
						
			// Cret the element settings object
			var element_settings = {};
			
			var system = jQuery(this).parents('.node').attr('id');
			system = system.split('-');
			system = '/'+system[0]+'/'+system[1]
			
			var href = jQuery(this).attr('href');
			var pieces = href.split("?");
			if (jQuery(this).parents().hasClass('pager-next')) {
				jQuery(this).html('show newer');
				if (pieces[1]) {
					element_settings.url = system+'/comments/ajax/next?'+pieces[1];
				} else {
					element_settings.url = system+'/comments/ajax/next';
				}
			} else if (jQuery(this).parents().hasClass('pager-previous')) {
				jQuery(this).html('show older');
				if (pieces[1]) {
					element_settings.url = system+'/comments/ajax/previous?'+pieces[1];
				} else {
					element_settings.url = system+'/comments/ajax/previous';
				}
			}
			
			// Get rid of the progress
			element_settings.progress = { 'type' : 'none' };
			
			// setup the click elements and add the href
			element_settings.event = 'click';
			
			element_settings.effect = 'fade';
								
			// Get the base
			var base = jQuery(this).attr('id');
			// var base = jQuery(this).attr('class');
										
			// Register the Ajax Request with Drupal
			Drupal.ajax[base] = new Drupal.ajax(base, this, element_settings);
					
		});
		
		// Auto Resize the Textarea
		jQuery('#post-node-form #edit-body-und-0-value').once().autoResize({
		  extraSpace : 13,
		  animate : false
		});
		jQuery('.comment-form textarea').once().autoResize({
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
				var period = (hours < 12) ? 'am' : 'pm';
				var minutes = date.getMinutes();
				var fulldate = String(year)+String(month)+String(day);
				var current = new Date();
				var fullcurrent = String(current.getFullYear())+String(current.getMonth())+String(current.getDate());
				var today = (fulldate == fullcurrent) ? true : false;
				month = month+1;
				hours = (hours < 13) ? hours : hours-12;
				minutes = (minutes < 10) ? '0'+minutes : minutes;
				if (today) {
					jQuery(this).text(hours+':'+minutes+period);
				} else {
					jQuery(this).text(month+'/'+day+'/'+year+' '+hours+':'+minutes+period);
				}
			}
		});
		
		// When an ajax form is submited, give the user some indication of this by adding a class to the form
		jQuery('.comment-form .form-submit.ajax-processed').once().mousedown(function() {
			var id = jQuery(this).parents('.node').attr('id');
			jQuery(id+' .comment-form').addClass('progress');
		});
		
		// ReAttach the Drupal Javascript Behaviors
  	jQuery.fn.reAttach = function(data) {
  		Drupal.attachBehaviors();
  	};

		
		// Cancel Deletion remove Delete form and fade in comment
		jQuery('.comment + .confirm-delete .form-actions a').click(function (event) {
			event.preventDefault();
			var comment = jQuery(this).parents('.confirm-delete').prev('.comment');
			jQuery(this).parents('.confirm-delete').fadeOut(400, function() {
				jQuery(this).remove();
				comment.fadeIn();
			});
		});
  
  	// Cancel Deletion remove Delete form and fade in comment
		jQuery('.node + .confirm-delete .form-actions a').click(function (event) {
			event.preventDefault();
			var comment = jQuery(this).parents('.confirm-delete').prev('.node');
			jQuery(this).parents('.confirm-delete').fadeOut(400, function() {
				jQuery(this).remove();
				comment.fadeIn();
			});
		});
  	
  }
  
}

