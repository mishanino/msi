/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Magento_InventoryCatalog/product/grid/cell/source-items-cell.html'
        },

        /**
         * Get source items data (source name and qty)
         *
         * @param {Object} record - Record object
         * @returns {Array} Result array
         */
        getSourceItemsData: function (record) {
            var result = record[this.index] ? record[this.index] : [];

            return result;
        }
    });
});
