<?php

namespace Sydor\Holidays\Model;


class Holidays extends \Magento\Framework\Model\AbstractModel implements
    \Magento\Framework\DataObject\IdentityInterface
{
    const NOROUTE_ENTITY_ID = 'no-route';
    const ENTITY_ID = 'entity_id';
    const CACHE_TAG = 'sydor_holidays';
    protected $_cacheTag = 'sydor_holidays';
    protected $_eventPrefix = 'sydor_holidays';

    public function _construct()
    {
        $this->_init(\Sydor\Holidays\Model\ResourceModel\Holidays::class);
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
