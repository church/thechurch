jQuery(document).ready(function() {

	// Add body classes basead on Browser and Operating System
	jQuery('body').addClass(jQuery.browser.name).addClass(jQuery.browser.className).addClass(jQuery.layout.name).addClass(jQuery.layout.className).addClass(jQuery.os.name);
  
  
  if (jQuery('.title').height() > 33) {
  	jQuery('.citytitle').css('display', 'block');
  }
  
  jQuery('#post-node-form #edit-body-und-0-value').autoResize({
    extraSpace : 13,
    animate : false
	});
  
});