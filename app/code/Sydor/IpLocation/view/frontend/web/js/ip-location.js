define([
    'jquery',
    'ko',
    'uiComponent',
    'mage/storage',
    'mage/translate'
], function ($, ko, Component, storage) {
    'use strict';

    return Component.extend({
        defaults: {
            configApiKey: null,
            apiUrl: 'https://api.ipstack.com/',
            apiKey: 'f1b516d4e7d533fd7bf9c45ba3677b58', // For Test
            testIp: '176.105.209.71', // For Test
            userIp: null,
            locationQuestion: ko.observable(null),
            locationCity: ko.observable(null),
            inputCity: ko.observable(null),
            locationSelector: '',
            citiesAutocomplete: ko.observable([]),
            debounceTimer: null,
            fetchError: null,
            responseErrorCodes: {101 : 'invalid_key', 104 : 'limit_reached'}
        },

        initialize: function () {
            this._super();
            let self = this;

            if (this.configApiKey) {
                this.apiKey = this.configApiKey;
            }

            if (!!this.userIp === false) {
                this.userIp = this.testIp;
            }

            this.locationCity.subscribe(function(newCity){
                self.locationQuestion(self.setLocationStateQuestion(newCity));
            });

            this.setLocationByIp(this.userIp);

            this.inputCity.subscribe(this.debounceFetchCities.bind(this));
        },

        debounceFetchCities: function (newValue) {
            clearTimeout(this.debounceTimer);

            this.debounceTimer = setTimeout(() => {
                this.fetchAutocompleteCities(newValue);
            }, 1000);
        },

        setLocationStateQuestion: function (city) {
            return $.mage.__('Is your city %1?').replace('%1', city);
        },

        setLocationByIp: function (userIp) {
            let url = this.apiUrl + userIp + '?access_key=' + this.apiKey,
                self = this;

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }

                    return response.json();
                })
                .then(data => {
                    if (data.success === false) {
                        console.error('API error:', data.error.info);
                        self.fetchError = data.error.info;
                    }

                    self.locationCity(data?.city || 'Undetermined');
                    self.showTarget('#location-selector');
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                });
        },

        fetchAutocompleteCities: function (query) {
            if (!query) {
                this.citiesAutocomplete([]);
                return;
            }

            let self = this,
                url = window.location.origin + '/rest/V1/locations/autocomplete';

            storage.post(
                url,
                JSON.stringify({ query: query })
            ).done(function (response) {
                self.citiesAutocomplete(response);
            }).fail(function (error) {
                self.citiesAutocomplete([]);
                console.log(error);
            });
        },

        showTarget: function($target){
            $(document).find($target).show();
        },

        hideTarget: function ($target) {
            $(document).find($target).hide();
        },

        selectCityAutocomplete: function (city) {
            this.locationCity(city);
            this.inputCity(city);
            this.citiesAutocomplete([]);
            this.hideTarget('#select-city-manually');
            this.hideTarget('#location-selector');
        }
    });
});
