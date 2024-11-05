<?php
namespace Sydor\Holidays\Model\ResourceModel\Holidays;

class Collection extends
    \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';

    public function _construct()
    {
        $this->_init(
            \Sydor\Holidays\Model\Holidays::class,
            \Sydor\Holidays\Model\ResourceModel\Holidays::class
        );
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }
}
