$(function() {
   'use strict';

    $(document).ready(function() {
        $('.js--edit-student-row').on('click', function () {
            var _this = $(this);
            var studentData = _this.data('item');
            var _form = $('.js--add-or-edit-student-row:eq(0)');
            _form.attr('action', _this.data('url'));
            _form.find('input,select,textarea').each(function() {
                var _this = $(this);
                var dataKey = getPropertyNameFromInputName(_this.prop('name'));
                _this.val(studentData[dataKey]);
                if (studentData.hasOwnProperty('student_id') && studentData['student_id']) {
                    if (dataKey === 'student_id' && dataKey === 'student_name') {
                        _this.prop('disabled', true);
                    }
                }
            });
        });
        $('.js--clear-student-form').on('click', function() {
            var _this = $(this);
            var _form = _this.closest('form');
            _form.attr('action', _this.data('url'));
            _form.find('input,select,textarea').each(function() {
                $(this).val(null).prop('disabled', false);
            });
        });

        function getPropertyNameFromInputName($inputName) {
            var name = $inputName.slice($inputName.indexOf('['));
            name = name.replace('[', '');
            name = name.replace(']', '');
            return name;
        }
    });
});
