$(function () {
    'use strict';

    $(document).ready(function() {
        function calculateTotalPayment() {
            var cache = prepareValues($("#moneystud-value_cash").val());
            var card = prepareValues($("#moneystud-value_card").val());
            var bank = prepareValues($("#moneystud-value_bank").val());
            $("#total_payment").text((cache + card + bank).toFixed(2));
        }
        function prepareValues(value) {
            value = value.replace(/,/gi, ".");
            value = parseFloat(value);
            value = Number.isNaN(value) ? 0 : value;
            return value;
        }
        $("#moneystud-value_cash").on("input", calculateTotalPayment);
        $("#moneystud-value_card").on("input", calculateTotalPayment);
        $("#moneystud-value_bank").on("input", calculateTotalPayment);
    });
});