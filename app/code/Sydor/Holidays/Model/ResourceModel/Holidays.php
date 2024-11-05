<?php

namespace Sydor\Holidays\Model\ResourceModel;

class Holidays extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function _construct()
    {
        $this->_init("holidays", "entity_id");
    }
}
