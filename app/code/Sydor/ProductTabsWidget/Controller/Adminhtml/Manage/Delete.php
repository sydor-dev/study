<?php

namespace Sydor\ProductTabsWidget\Controller\Adminhtml\Manage;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Sydor\ProductTabsWidget\Model\ProductTabsWidgetFactory;

class Delete extends Action
{
    public $widgetsFactory;

    public function __construct(
        Context                   $context,
        ProductTabsWidgetFactory $widgetsFactory
    )
    {
        $this->widgetsFactory = $widgetsFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        try {
            $widgetsModel = $this->widgetsFactory->create();
            $widgetsModel->load($id);
            $widgetsModel->delete();
            $this->messageManager->addSuccessMessage(__('You deleted the widget.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $resultRedirect->setPath('*/*/');
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Sydor_ProductTabsWidget::delete');
    }
}
