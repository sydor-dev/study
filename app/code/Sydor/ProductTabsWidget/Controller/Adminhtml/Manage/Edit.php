<?php

namespace Sydor\ProductTabsWidget\Controller\Adminhtml\Manage;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Sydor\ProductTabsWidget\Model\ProductTabsWidgetFactory;

class Edit extends Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Sydor\ProductTabsWidget\Model\ProductTabsWidgetFactory
     */
    protected $widgetsFactory;

    public function __construct(
        Context                   $context,
        Registry                  $coreRegistry,
        ProductTabsWidgetFactory $widgetsFactory
    )
    {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->widgetsFactory = $widgetsFactory;
    }

    public function execute()
    {
        $widgetsId = $this->getRequest()->getParam('id');
        $widgetsModel = $this->widgetsFactory->create()->load($widgetsId);
        $this->coreRegistry->register('product_tabs_widget_data', $widgetsModel);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__("Edit Widgets"));
        return $resultPage;
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Sydor_ProductTabsWidget::edit');
    }
}
