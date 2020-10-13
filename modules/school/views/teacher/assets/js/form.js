$(function () {
    'use strict';

    $(document).ready(
        function () {
            $(".js--address-field").suggestions({
                token: "9ac43b0c02b76d2f8be18c637ce94133d7c66e7f",
                type: "ADDRESS",
                count: 5,
                onSelect: function () {}
            });
        }
    );
});