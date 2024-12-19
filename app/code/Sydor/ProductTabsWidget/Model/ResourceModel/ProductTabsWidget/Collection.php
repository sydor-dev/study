<?php

namespace Sydor\ProductTabsWidget\Model\ResourceModel\ProductTabsWidget;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Sydor\ProductTabsWidget\Model\ProductTabsWidget as Model;
use Sydor\ProductTabsWidget\Model\ResourceModel\ProductTabsWidget as ResourceModel;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';

    public function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);

        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }
}
