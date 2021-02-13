$(function () {
    'use strict';

    $(document).ready(function() {
        $('.js--change-user-password').on('click', function() {
            const $this = $(this);
            const $inputGroup = $this.closest('td').find('.input-group:eq(0)');
            $inputGroup.show();
            $this.hide();
        });
        $('.js--save-user-password').on('click', function() {
            const $this = $(this);
            const $data = new FormData();
            $this.closest('td').find('input').each(function() {
                const $this = $(this);
                $data.append($this.prop('name'), $this.val());
                console.log($this.prop('name'), $this.val());
            });
            $.ajax({
                method: 'POST',
                url: $this.data('url'),
                data: $data,
                processData: false,
                contentType: false,
                success: function($data) {
                    if ($data.success) {
                        window.location.reload();
                    } else {
                        const $formGroupDiv = $this.closest('.form-group');
                        $formGroupDiv.find('.help-block:eq(0)').text($data.message ? $data.message : 'Произошла ошибка').show();
                        $formGroupDiv.addClass('has-error');
                    }
                },
            });
        });
    });
});