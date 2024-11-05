<?php

namespace Sydor\Holidays\Block\Adminhtml\Holidays;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Sydor_Holidays';
        $this->_controller = 'adminhtml_holidays';
        parent::_construct();
        $this->buttonList->remove('delete');
    }
}
