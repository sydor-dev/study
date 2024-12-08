<?php

namespace Sydor\IpLocation\Api;

interface LocationsAutocompleteInterface
{
    /**
     *
     * @param string $query
     * @return string[]
     */
    public function getLocationsAutocomplete(string $query): array;
}
