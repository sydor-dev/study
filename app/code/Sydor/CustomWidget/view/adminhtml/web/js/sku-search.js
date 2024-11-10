define([
    'uiComponent',
    'ko',
    'mage/storage',
    'jquery',
    'mage/translate',
], function(
    Component,
    ko,
    storage,
    $,
    $t,
) {
    'use strict';

    return Component.extend({
        defaults: {
            placeholder: $t('Example: %1').replace('%1', '24-MB01'),
            messageResponse: ko.observable(''),
            isSuccess: ko.observable(false),
            sku: ko.observable(''),
            suggestions: []
        },
        initialize: function() {
            this._super();
            console.log('The skuSearch component has been loaded.');
        },

        delayedSearch: function () {
            $('body').trigger('processStart');
            this.messageResponse('');
            this.isSuccess(false);
            this.suggestions = [];

            storage.get(`rest/V1/products/${this.sku()}`)
                .done(response => {
                    this.messageResponse($t('Product found! %1').replace('%1', `<strong>${response.name}</strong>`));
                    this.isSuccess(true);
                    console.log(this.messageResponse())
                })
                .fail(() => {
                    this.messageResponse($t('Product not found.'));
                    this.isSuccess(false);
                })
                .always(() => {
                    $('body').trigger('processStop');
                });
        },

        selectSku: function(sku) {
            this.sku(sku.name);
            this.suggestions = [];
        }
    });
});


