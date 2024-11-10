define([
    'ko',
    'jquery',
    'uiComponent',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/modal/confirm',
    'Magento_Customer/js/customer-data',
    'mage/url',
    'mage/template',
    'mage/translate',
    'mage/validation'
], function (ko, $, Component, modal, confirm, customerData, urlBuilder, mageTemplate, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            popupTemplate: 'Sydor_OneClickPurchase/view/popup',
            formSelector: '#one-click-purchase-form',
            popupInitialized: false,
            popupContainer: $('#one-click-purchase-popup'),
            phoneNumber: ko.observable(''),
            email: ko.observable(''),
            userName: ko.observable(''),
            buttonText: $t('Instant Purchase'),
            purchaseUrl: urlBuilder.build('oneclickpurchase/checkout/placeOrder')
        },

        validate: function () {
            return $(this.formSelector).validation()
                && $(this.formSelector).validation('isValid');
        },

        openPopup: function () {
            if (!this.popupInitialized){
                this.initPopup();
            }
            this.popupInstance.openModal();
            this.applyPopupBindings();
        },

        closePopup: function () {
            this.popupInstance.closeModal();
        },


        initPopup: function() {
            if (!this.isPopupExists()) {
                return false;
            }

            var options = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                buttons: []
            };

            this.popupInstance = modal(options, this.popupContainer);
            this.popupInitialized = true;
            this.setAdditionalData();
        },

        setAdditionalData: function () {
        },

        applyPopupBindings: function () {
            var self = this;
            if (self.popupContainer.length > 0) {
                ko.cleanNode(self.popupContainer[0]);
                ko.applyBindings(self, self.popupContainer[0]);
            }
        },

        isPopupExists: function (){
            return this.popupContainer && this.popupContainer.length > 0;
        },

        appendProductAttributes: function(){
            let productAttributes = '';
            $(document).find('input.super-attribute-select, input[name=qty]').each(function(){
                if ($(this).val() !== ''){
                    productAttributes += '&' + $(this).attr('name') + '=' + $(this).val();
                }
            });

            return productAttributes;
        },

        submitPurchase: function() {
            if (!this.validate()){
                return;
            }

            let self = this,
                purchaseData = $(document).find(self.formSelector).serialize();

            purchaseData += this.appendProductAttributes();
            $.ajax({
                url: this.purchaseUrl,
                data: purchaseData,
                type: 'post',
                dataType: 'json',
                beforeSend: function () {
                    $('body').trigger('processStart');
                },
                success: function(response) {
                    console.log('Form submitted successfully!');
                    self.closePopup();
                },
                error: function() {
                    console.error('Form submission failed.');
                }
            }).always(function () {
                $('body').trigger('processStop');
            });
        }
    });
});
