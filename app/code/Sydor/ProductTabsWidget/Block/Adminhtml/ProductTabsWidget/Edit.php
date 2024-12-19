<?php

namespace Sydor\ProductTabsWidget\Block\Adminhtml\ProductTabsWidget;

use Magento\Backend\Block\Widget\Form\Container;

class Edit extends Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Sydor_ProductTabsWidget';
        $this->_controller = 'adminhtml_productTabsWidget';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Tab'));
        $this->buttonList->add('save_and_continue', [
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => [
                        'event' => 'saveAndContinueEdit',
                        'target' => '#edit_form'
                    ]
                ]
            ]
        ], -100);
        $this->buttonList->update('delete', 'label', __('Delete Tab'));
    }
}
