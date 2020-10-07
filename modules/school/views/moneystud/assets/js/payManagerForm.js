$(function () {
    'use strict';

    $(document).ready(function() {
        var $form = $('.payment-form > form').eq(0);
        var officeSearchUrl = $form.data('office-search-url');
        var $officeListTemplate = $('#moneystud-calc_office').clone();
        $('#js--autocomplete-hidden').on('change', function () {
            var $this = $(this);
            $.ajax({
                method: "GET",
                url: officeSearchUrl + "?id=" + $this.val(),
            }).done(function(result) {
                $('#moneystud-calc_office').html('');
                $('#moneystud-calc_office').append($officeListTemplate.find('option').clone());
                $('#moneystud-calc_office').val(null);
                if (Array.isArray(result) && result.length) {
                    var $selectOfficeElement = $('#moneystud-calc_office');
                    var mainOfficeId = null;
                    result.forEach(function (item) {
                        var $option = $('#moneystud-calc_office').find('option[value="' + item.id + '"]');
                        if ($option.length === 1) {
                            $option.html((item.isMain === '1' ? '&#xf005;' : '&#xf006') + ' ' + $option.text());
                        }
                        if (item.isMain === '1') {
                            mainOfficeId = item.id;
                        }
                    });
                    if (mainOfficeId) {
                        var $option = $('#moneystud-calc_office').find('option[value="' + mainOfficeId + '"]');
                        if ($option.length === 1) {
                            $selectOfficeElement.val(mainOfficeId);
                        }
                    } else {
                        var $option = $('#moneystud-calc_office').find('option[value="' + result[0].id + '"]');
                        if ($option.length === 1) {
                            $selectOfficeElement.val(result[0].id);
                        }
                    }
                }
            });
        });
    });
});
