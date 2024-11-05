<?php
namespace Sydor\Holidays\Controller\Adminhtml\Manage;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
class Edit extends Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Sydor\Holidays\Model\HolidaysFactory
     */
    protected $holidaysFactory;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Sydor\Holidays\Model\HolidaysFactory $holidaysFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->holidaysFactory = $holidaysFactory;
    }
    public function execute()
    {
        $holidaysId = $this->getRequest()->getParam('id');
        $holidaysModel = $this->holidaysFactory->create()->load($holidaysId);
        $this->coreRegistry->register('holidays_data', $holidaysModel);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__("Edit Holidays"));
        return $resultPage;
    }
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Sydor_Holidays::edit');
    }
}
