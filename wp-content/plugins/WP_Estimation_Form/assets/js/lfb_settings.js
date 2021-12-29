(function ($) {
    "use strict";
        
    $(document).ready(function () {
        $('a[data-action="lfb_settings_checkLicense"]').on('click', lfb_settings_checkLicense);

    });
    function lfb_settings_checkLicense() {
        var error = false;
        var $field = jQuery('#lfb_settings_licenseContainer input[name="purchaseCode"]');
        if ($field.val().length < 9) {
            $field.parent().addClass('has-error');
        } else {
            lfb_showLoader();
            jQuery.ajax({
                url: ajaxurl,
                type: 'post',
                data: {action: 'lfb_checkLicense', code: $field.val()},
                success: function (rep) {
                    jQuery('#lfb_loader').fadeOut();
                    if (rep == '1') {
                        $field.parent().addClass('has-error');
                        setTimeout(function () {
                            document.location.reload();
                        }, 1000);
                    } else {
                        document.location.reload();
                    }
                }
            });
        }
    }
    function lfb_showLoader() {
        jQuery('html,body').animate({scrollTop: 0}, 250);
        jQuery('#lfb_loader').fadeIn();
    }
    
})(jQuery);