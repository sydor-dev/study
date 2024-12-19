<?php

namespace Sydor\ProductTabsWidget\Model\ResourceModel;

class ProductTabsWidget extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function _construct()
    {
        $this->_init("product_tabs_widget", "entity_id");
    }
}
