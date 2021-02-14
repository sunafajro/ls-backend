$(function () {
    'use strict';

    $('#balance').click(
        function () {
            if($('#fullbalance').is(':visible')) {
                $("#fullbalance").hide();
            } else {
                $("#fullbalance").show();
            }
        }
    );
    $('[data-toggle="popover"]').popover();
    $('[data-toggle="tooltip"]').tooltip();
});