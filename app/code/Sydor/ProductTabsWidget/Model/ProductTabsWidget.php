<?php

namespace Sydor\ProductTabsWidget\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Sydor\ProductTabsWidget\Model\ResourceModel\ProductTabsWidget as ResourceModel;
class ProductTabsWidget extends AbstractModel implements IdentityInterface
{
    const NOROUTE_ENTITY_ID = 'no-route';
    const ENTITY_ID = 'entity_id';
    const CACHE_TAG = 'product_tabs_widget';
    protected $_cacheTag = 'product_tabs_widget';
    protected $_eventPrefix = 'product_tabs_widget';

    public function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    public function load($id, $field = null)
    {
        if($id === null) {
            return $this->noRoute();
        }
        return parent::load($id, $field);
    }

    public function noRoute()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
}

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }
}
