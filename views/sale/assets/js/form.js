$(function () {
    'use strict';

    $(document).ready(function() {
        $("#sale-procent").change(function(e) {
            if(e.target.value === "2") {
                $("#sale-base-block").show();
            } else {
                $("#sale-base-block").hide();
                $("#sale-base").val('');
            }
        });
    });
});