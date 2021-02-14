$(function() {
    'use strict';

    $(document).ready(function() {
        $("#call-calc_servicetype").change(function() {
            var key = $("#call-calc_servicetype option:selected").val();
            if(key === "1") {
                $("#hidden-field").show();
            } else {
                $("#hidden-field").hide();
            }
        });
    });
});
