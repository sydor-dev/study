<?php

namespace Sydor\ProductTabsWidget\Api;

interface ProductTabsWidgetInterface
{
    const string TABLE_NAME = 'product_tabs_widget';
    const string ID = 'entity_id';
    const array REQUIRED_FIELDS = [
        'title',
        'status',
        'show_pager',
        'products_per_page',
        'products_count',
        'conditions',
        'properties'
    ];
}
