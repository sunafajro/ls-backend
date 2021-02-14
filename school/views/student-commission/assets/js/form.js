$(function () {
    'use strict';

    $(document).ready(function() {
        $('#studentcommission-percent').on('change', function () {
            var _this = $(this);
            var percent = parseFloat(_this.val());
            var debt = parseFloat($('#studentcommission-debt').val());
            if (!isNaN(debt) && !isNaN(percent)) {
                var value = Math.round(debt * percent / 100);
                $('#studentcommission-value').val(Math.abs(value));
            }
        });
    });
});