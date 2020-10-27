$(function () {
    'use strict';

    function removeFile($el, id) {
        if (confirm("Вы действительно хотите удалить этот файл?")) {
            var url = $el.closest('.js--file-ids').data('delete-url');
            $.ajax({
                url: url + '/' + id,
                type: 'POST',
                success: function (data) {
                    if (data.success) {
                        $el.closest('div').remove();
                    } else {
                        alert('Не удалось удалить файл.');
                    }
                }
            });
        }
    }

    function addFileBlock($el, file) {
        var $newFileBlock = $('.js--file-block-template').clone();
        $newFileBlock.removeClass('js--file-block-template');
        $newFileBlock.removeClass('hidden');
        $newFileBlock.find('input').eq(0).val(file.fileId);
        $newFileBlock.find('.js--file-name').eq(0).text(file.fileName);
        $newFileBlock.find('.js--remove-file').eq(0).on('click', function () {
            removeFile($(this), file.fileId);
        });
        $el.closest('.js--files-block').find('.js--file-ids').append($newFileBlock);
    }

    $(document).ready(
        function() {
            $('.js--send-now-button').on('click', function () {
                var $this = $(this);
                $this.closest('div').find('.js--send-now-input').prop('disabled', false);
                $this.closest('form').submit();
            });
            $("#message-calc_messwhomtype").change(
                function() {
                    var key = $("#message-calc_messwhomtype option:selected").val();
                    $.ajax({
                        type:"POST",
                        url:"/school/message/ajaxgroup",
                        data: "type="+key,
                        success: function(users) {
                            $(".field-message-refinement_id").html(users);
                        }
                    });
                }
            );
            $('.js--upload-file-btn').on('click', function () {
                var $uploadInput = $(this).closest('.js--files-block').find('.js--upload-file').eq(0);
                $uploadInput.trigger('click');
            });
            $('.js--remove-file').on('click', function () {
                var $this = $(this);
                removeFile($this, $this.closest('div').find('input').eq(0).val());
            });
            $('.js--upload-file').on('change', function (e) {
                var $this = $(this);
                var formData = new FormData();
                formData.append('UploadForm[file]', e.target.files[0]);
                $.ajax({
                    url: $this.data('upload-url'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        if (data.success) {
                            addFileBlock($this, data);
                        } else {
                            alert('Не удалось загрузить файл.');
                        }
                        $this.val('');
                    }
                });
            });
        }
    );
});