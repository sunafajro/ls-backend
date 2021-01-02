$(function () {
    'use strict';

    $(document).ready(function() {
        $('div.panel').each(function () {
            const $this = $(this);

            const $submitAll = $this.find('.js--accrual-all-link').eq(0);
            const groupsData = {groups: []};
            $this.find('.panel-body .js--accrual-link').each(function () {
                const $this = $(this);
                const params = $this.data('params');
                if (params && params.hasOwnProperty('groups')) {
                    groupsData.groups.push(params.groups[0]);
                }
            });
            if (groupsData.groups.length) {
                $submitAll.attr('data-params', JSON.stringify(groupsData));
                $submitAll.show('disabled', false);
            }

            const $doneAll = $this.find('.js--accrual-done-all-link').eq(0);
            const accrualsData = {accruals: []};
            $this.find('.panel-body .js--accrual-done-link').each(function () {
                const $this = $(this);
                const params = $this.data('params');
                if (params && params.hasOwnProperty('accruals')) {
                    accrualsData.accruals.push(params.accruals[0]);
                }
            });
            if (accrualsData.accruals.length) {
                $doneAll.attr('data-params', JSON.stringify(accrualsData));
                $doneAll.show('disabled', false);
            }
        });
    });
});