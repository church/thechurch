jQuery(document).ready(function() {
    
  Stripe.setPublishableKey(stripe);
  
  $('form.donate').submit(function(event) {
  
    var form = $(this);

    // Disable the submit button to prevent repeated clicks
    jQuery('input.submit', this).attr('disabled', true);

    Stripe.createToken(form, stripeResponseHandler);

    // Prevent the form from submitting with the default action
    event.preventDefault();
    
  });
  
});

var stripeResponseHandler = function(status, response) {

  var form = jQuery('form.donate');

  if (response.error) {
  
    // Show the errors on the form
    jQuery('.payment-errors', form).text(response.error.message);
    jQuery('input.submit', form).attr('disabled', false);
    
  } else {
  
    // token contains id, last4, and card type
    var token = response.id;
    // Insert the token into the form so it gets submitted to the server
    
    jQuery('#donate_stripe_token').val(token);
    
    // and submit
    jQuery.post(form.attr('action'), form.serialize());
    
  }
  
};