$(function() {
    'use strict';

    $(".js--previous-times").on('click', function () {
        $("#journalgroup-time_begin").val($(this).data('begin'));
        $("#journalgroup-time_end").val($(this).data('end'));
    });
    $(".js--student-status").on('change', function() {
        var _this = $(this);
        _this.closest('.row').find('.js--comment-for-student').prop('required', _this.val() === '1');
    });
});