function sendSMTPwishlist(from,to,subject) {
    jQuery.ajax({
        type : "post",
        data: {action: 'send_SMTP_wishlist', from: from, to: to, subject: subject, security: mailing_ajax_object.security},
        dataType : 'text',
        url : mailing_ajax_object.ajax_url,
        success: function(response) {

        }
    })
}