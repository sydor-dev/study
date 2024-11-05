<?php
namespace Sydor\Holidays\Controller\Adminhtml\Manage;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
class Delete extends Action
{
    public $holidaysFactory;

    public function __construct(
        Context $context,
        \Sydor\Holidays\Model\HolidaysFactory $holidaysFactory
    ) {
        $this->holidaysFactory = $holidaysFactory;
        parent::__construct($context);
    }
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        try {
            $holidaysModel = $this->holidaysFactory->create();
            $holidaysModel->load($id);
            $holidaysModel->delete();
            $this->messageManager->addSuccessMessage(__('You deleted the holiday.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $resultRedirect->setPath('*/*/');
    }
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Sydor_Holidays::delete');
    }
}
