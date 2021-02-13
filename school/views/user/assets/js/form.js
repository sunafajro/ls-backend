$(function () {
    'use strict';
    $(document).ready(
        function () {
            $("#user-status").change(
                function () {
                    if ($("#user-status option:selected").val() == 4) {
                        $("#user-calc_city").prop("disabled", false);
                        $("#user-calc_office").prop("disabled", false);
                    } else {
                        $("#user-calc_city")
                            .prop("selectedIndex", 0)
                            .prop("disabled", true);
                        $("#user-calc_office")
                            .prop("selectedIndex", 0)
                            .prop("disabled", true);
                    }
                }
            );
        }
    );
});