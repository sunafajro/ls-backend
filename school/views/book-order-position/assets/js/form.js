$(function() {
    'use strict';

    $(document).ready(function() {
        $('#bookorderposition-count').on('change', function () {
            var cost = $('#js--book-cost-value').val();
            $('#bookorderposition-paid').val($(this).val() * cost);
        });
    });
});