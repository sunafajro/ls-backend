$(function () {
    'use strict';

    $(document).ready(function() {
        $('.js--change-group-params-btn').on('click', function() {
          var _this = $(this);
          $.ajax({
              method: 'POST',
              url: _this.data('url'),
          }).always(function () {
              window.location.reload();
          });
        });
      });
});