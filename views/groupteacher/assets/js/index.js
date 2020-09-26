$(function () {
    'use strict';

    $(document).ready(function() {
        $('.js--change-group-param').on('click', function() {
            var _this = $(this);
            var _itemList = _this.closest('td').find('.js--item-list');
            var _itemName = _this.closest('td').find('.js--item-name');
            _itemList.show();
            _itemName.hide();
        });
        $('.js--save-group-param').on('click', function() {
            var _this = $(this);
            var _itemList = _this.closest('td').find('.js--item-list');
            var _itemName = _this.closest('td').find('.js--item-name');
            var baseUrl = _this.data('url');
            var search = 'name=' + _this.data('name') + '&value=' + _itemList.find('select').val();
            var url = baseUrl + (baseUrl.indexOf('?') !== -1 ? '&' : '?') + search;
            $.ajax({
                method: 'POST',
                url: url,
            }).always(function () {
                window.location.reload();
            });
        });
    });
});