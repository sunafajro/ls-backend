$(function () {
    'use strict';

    $(document).ready(function() {
        $('div.panel').each(function () {
            var _this = $(this);
            var $submitAll = _this.find('.js--accrual-all-link').eq(0);
            var data = {groups: []};
            _this.find('.panel-body .js--accrual-link').each(function () {
                var _this = $(this);
                var params = _this.data('params');
                if (params && params.hasOwnProperty('groups')) {
                    data.groups.push(params.groups[0]);
                }
            });
            if (data.groups.length) {
                $submitAll.attr('data-params', JSON.stringify(data));
                $submitAll.show('disabled', false);
            }
        });
    });
});