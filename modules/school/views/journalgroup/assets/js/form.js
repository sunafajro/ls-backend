$(function () {
    'use strict';

    $(document)
        .on('click', '.js--previous-times', function () {
            const $this = $(this);
            $('#journalgroup-time_begin').val($this.data('begin'));
            $('#journalgroup-time_end').val($this.data('end'));
        })
        .on('change', '.js--student-status', function () {
            const $this = $(this);
            $this.closest('.row').find('.js--comment-for-student').prop('required', $this.val() === '1');
        })
        .on('change', '.js--lesson-location-type', function (e) {
            const $this = $(this);
            const $form = $this.closest('form');
            $form.find('input.js--student-successes').each(function () {
                $(this).prop('readonly', e.target.value !== 'online');
                if (e.target.value !== 'online') {
                    $(this).val(0);
                }
            });
        });
});