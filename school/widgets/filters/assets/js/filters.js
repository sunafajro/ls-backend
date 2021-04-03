/* global moment */
/* global fieldDateFormat */
$(function () {
    'use strict';

    if (fieldDateFormat === undefined) {
        const fieldDateFormat = 'YYYY-MM-DD';
    }

    var dateChangeBtnBlock = $('.js--change-dates-btn-block').eq(0);
    var dateStartInput     = dateChangeBtnBlock ? dateChangeBtnBlock.data('start-date') : undefined;
    var dateEndInput       = dateChangeBtnBlock ? dateChangeBtnBlock.data('end-date') : undefined;

    function setDates(currentDate, $dateStartInput, $dateEndInput, type) {
        $dateStartInput.val(currentDate.startOf(type).format(fieldDateFormat)).trigger('change');
        $dateEndInput.val(currentDate.endOf(type).format(fieldDateFormat)).trigger('change');
    }

    function changeDates(type) {
        var currentDate = moment();
        var $dateStartInput = $('.' + dateStartInput).eq(0);
        var $dateEndInput = $('.' + dateEndInput).eq(0);
        var startDateValue = $dateStartInput.val();
        switch (type) {
            case 'currentWeek':
                setDates(currentDate, $dateStartInput, $dateEndInput, 'week');
                break;
            case 'previousWeek':
                if (moment(startDateValue, fieldDateFormat).isValid()) {
                    currentDate = moment(startDateValue, fieldDateFormat);
                }
                currentDate.subtract(1, 'weeks');
                setDates(currentDate, $dateStartInput, $dateEndInput, 'week');
                break;
            case 'nextWeek':
                if (moment(startDateValue, fieldDateFormat).isValid()) {
                    currentDate = moment(startDateValue, fieldDateFormat);
                }
                currentDate.add(1, 'weeks');
                setDates(currentDate, $dateStartInput, $dateEndInput, 'week');
                break;
            case 'currentMonth':
                setDates(currentDate, $dateStartInput, $dateEndInput, 'month');
                break;
            case 'previousMonth':
                if (moment(startDateValue, fieldDateFormat).isValid()) {
                    currentDate = moment(startDateValue, fieldDateFormat);
                }
                currentDate.subtract(1, 'months');
                setDates(currentDate, $dateStartInput, $dateEndInput, 'month');
                break;
            case 'nextMonth':
                if (moment(startDateValue, fieldDateFormat).isValid()) {
                    currentDate = moment(startDateValue, fieldDateFormat);
                }
                currentDate.add(1, 'months');
                setDates(currentDate, $dateStartInput, $dateEndInput, 'month');
                break;
        }
    }

    $(document).ready(function () {
        $('.js--change-dates-btn').on('click', function () {
            changeDates($(this).data('type'));
        });
    });
});