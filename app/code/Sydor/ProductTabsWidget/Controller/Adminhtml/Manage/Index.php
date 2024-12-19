<?php

namespace Sydor\ProductTabsWidget\Controller\Adminhtml\Manage;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    public $context;
    public $resultPageFactory;

    public function __construct(
        Context     $context,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Sydor_ProductTabsWidget::manage');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage'));
        return $resultPage;
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Sydor_ProductTabsWidget::manage');
    }
}
