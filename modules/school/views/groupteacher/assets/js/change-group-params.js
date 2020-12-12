$(function () {
    'use strict';

    $(document).ready(function() {
        $('.js--change-group-params-btn').on('click', function() {
          $.ajax({
              method: 'POST',
              url: $(this).data('url')
          }).always(function () {
              window.location.reload();
          });
        });
      });
});