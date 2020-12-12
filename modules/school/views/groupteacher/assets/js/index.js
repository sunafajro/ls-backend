$(function () {
    'use strict';

    $(document).ready(function() {
        $('.js--change-group-param').on('click', function() {
            const $this = $(this);
            const $itemList = $this.closest('td').find('.js--item-list');
            const $itemName = $this.closest('td').find('.js--item-name');
            $itemList.show();
            $itemName.hide();
        });
        $('.js--save-group-param').on('click', function() {
            const $this = $(this);
            const $itemList = $this.closest('td').find('.js--item-list');
            const baseUrl = $this.data('url');
            const search = 'name=' + $this.data('name') + '&value=' + $itemList.find('select').val();
            const url = baseUrl + (baseUrl.indexOf('?') !== -1 ? '&' : '?') + search;
            $.ajax({
                method: 'POST',
                url: url
            }).always(function () {
                window.location.reload();
            });
        });
    });
});