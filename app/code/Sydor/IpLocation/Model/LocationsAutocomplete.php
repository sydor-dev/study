<?php

namespace Sydor\IpLocation\Model;

use Sydor\IpLocation\Api\LocationsAutocompleteInterface;

class LocationsAutocomplete implements LocationsAutocompleteInterface
{
    /**
     * @inheritDoc
     */
    public function getLocationsAutocomplete(string $query): array
    {
        $allCities = ['Kyiv', 'Shostka', 'Sumy', 'Chapliivka', 'Dnipro'];

        return array_filter($allCities, function ($city) use ($query) {
            return stripos($city, $query) !== false;
        });
    }
}
