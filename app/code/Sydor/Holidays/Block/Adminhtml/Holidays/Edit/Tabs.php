<?php

namespace Sydor\Holidays\Block\Adminhtml\Holidays\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    )
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('holidays_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Holidays Data'));
    }

    protected function _prepareLayout()
    {
        $this->addTab(
            'general',
            [
                'label' => __('General Settings'),
                'content' => $this->getLayout()->createBlock(
                    'Sydor\Holidays\Block\Adminhtml\Holidays\Edit\Tab\General'
                )->toHtml(),
                'active' => true
            ]
        );

        $this->addTab(
            'additional',
            [
                'label' => __('Additional Settings'),
                'content' => $this->getLayout()->createBlock(
                    'Sydor\Holidays\Block\Adminhtml\Holidays\Edit\Tab\Additional'
                )->toHtml()
            ]
        );

        return parent::_prepareLayout();
    }
}
