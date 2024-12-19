<?php

namespace Sydor\ProductTabsWidget\Controller\Adminhtml\Manage;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use DateTime;
use Sydor\ProductTabsWidget\Model\ProductTabsWidgetFactory;
use Sydor\ProductTabsWidget\Api\ProductTabsWidgetInterface;

class Save extends Action
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
        $data = $this->getRequest()->getParams();

        if (isset($data['entity_id']) && $data['entity_id']) {
            $model = $this->widgetsFactory->create()->load($data['entity_id']);
        } else {
            $model = $this->widgetsFactory->create();
        }

        $this->saveFields($model, $data);
        $model->save();

        $this->messageManager->addSuccess(__('You have updated the widget successfully.'));

        return $resultRedirect->setPath('*/*/');
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Sydor_ProductTabsWidget::save');
    }

    private function saveFields(&$model, &$data)
    {
        $this->mergeParametersIntoData($data);

        foreach (ProductTabsWidgetInterface::REQUIRED_FIELDS as $field){
            if (isset($data[$field])) {
                $model->setData($field, is_array($data[$field]) ? json_encode([$field => $data[$field]]) : $data[$field]);
            }
        }

        if (isset($data['store_view'])) {
            $model->setStoreView(implode(',', $data['store_view']));
        } else {
            $model->setStoreView('');
        }
    }

    private function mergeParametersIntoData(&$data)
    {
        if (isset($data['parameters'])){
            $data = array_merge($data, $data['parameters']);
            unset($data['parameters']);
        }
    }
}
