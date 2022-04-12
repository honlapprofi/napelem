jQuery('document').ready(function ($) {
  function sgbvSave() {
    $('.sgbv-settings-loading').fadeIn(function () {
      var form = $('form#sgbv-settings-form');
      var data = {
        action: 'sgbv_save'
      };
      $(form)
        .find(':input')
        .each(function () {
          data[this['name']] = $(this).val();
        });
      data['active_custom_types'] = getActiveCustomPostTypes().join();
      $.ajax({
        url: sgbv.ajaxurl,
        data: data,
        type: $(form).attr('method'),
        headers: {
          'X-WP-Nonce': sgbv.nonce
        },
        complete: function (response) {
          $('.sgbv-settings-loading').fadeOut();
        }
      });
    });
  }

  $('#simple-googlebot-visit-admin .admin-block > a').click(function (e) {
    e.preventDefault();
    $('.admin-block').addClass('admin-block-hidden');
    $(this).parents('.admin-block').removeClass('admin-block-hidden');
  });

  $('form#sgbv-settings-form .sgbv-field-info').click(function (e) {
    e.preventDefault();
    var field = $(this).parents('.sgbv-field');
    var currentValue = $(field).find('select').val();
    var newValue = currentValue == 'true' ? 'false' : 'true';
    $(field).find('select').val(newValue);
    $(field).attr('data-value', newValue);
    sgbvSave();
  });

  /*$('form#sgbv-settings-formrrrr :input').change(function (e) {
    e.preventDefault();
    sgbvSave();
  });*/

  $('form#sgbv-settings-form .custom-post-types-list li a').click(function (e) {
    e.preventDefault();
    var customPostType = $(e.currentTarget).parent().attr('data-value');
    $(
      `form#sgbv-settings-form .custom-post-types-list li[data-value="${customPostType}"]`
    ).remove();
    sgbvSave();
  });

  $('form#sgbv-settings-form').keyup(function (e) {
    e.preventDefault();
    if (e.keyCode == 13) {
      addNewPostType();
    }
  });

  $('form#sgbv-settings-form').submit(function (e) {
    e.preventDefault();
    return false;
  });

  $('form#sgbv-settings-form a.add-custom-post-type').click(function (e) {
    e.preventDefault();
    addNewPostType();
  });

  function addNewPostType() {
    var value = $('form#sgbv-settings-form input[name="active_custom_types"]')
      .val()
      .trim();
    $('form#sgbv-settings-form input[name="active_custom_types"]').val('');
    var excludeActivePostTypes = ['post', 'entries', 'product'];
    var currentActivePostTypes = getActiveCustomPostTypes();
    if (
      value &&
      currentActivePostTypes.indexOf(value) === -1 &&
      excludeActivePostTypes.indexOf(value) === -1
    ) {
      var customTypeHtml = $('<div />')
        .append(
          $(
            'form#sgbv-settings-form .custom-post-types-list li[data-value="{CUSTOM_TYPE_VALUE}"]'
          ).clone()
        )
        .html();
      $('form#sgbv-settings-form .custom-post-types-list').append(
        customTypeHtml.replace(/\{CUSTOM_TYPE_VALUE\}/g, value)
      );

      $(
        'form#sgbv-settings-form .custom-post-types-list li:last-of-type a'
      ).click(function (e) {
        e.preventDefault();
        var customPostType = $(e.currentTarget).parent().attr('data-value');
        $(
          `form#sgbv-settings-form .custom-post-types-list li[data-value="${customPostType}"]`
        ).remove();
        sgbvSave();
      });
      sgbvSave();
    }
  }

  function getActiveCustomPostTypes() {
    var currentActivePostTypes = [];
    $('form#sgbv-settings-form .custom-post-types-list li').each(function (
      index,
      e
    ) {
      var currentValue = $(e).data('value').toString().trim();
      if (currentValue !== '{CUSTOM_TYPE_VALUE}') {
        currentActivePostTypes.push(currentValue);
      }
    });
    return currentActivePostTypes;
  }
});
