<?php

namespace Sydor\Holidays\Controller\Adminhtml\Manage;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use DateTime;

class Save extends Action
{
    public $holidaysFactory;

    public function __construct(
        Context $context,
        \Sydor\Holidays\Model\HolidaysFactory $holidaysFactory
    )
    {
        $this->holidaysFactory = $holidaysFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getParams();

        if (isset($data['entity_id']) && $data['entity_id']) {
            $model = $this->holidaysFactory->create()->load($data['entity_id']);
        } else {
            $model = $this->holidaysFactory->create();
        }

        $this->saveGeneralSettings($model, $data);
        $this->saveAdditionalSettings($model, $data);

        $model->save();
        $this->messageManager->addSuccess(__('You have updated the holiday successfully.'));

        return $resultRedirect->setPath('*/*/');
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Sydor_Holidays::save');
    }

    private function saveGeneralSettings(&$model, &$data)
    {
        $this->validateDateValues($data);

        $model->setTitle($data['title'])
              ->setContent($data['content'])
              ->setStatus($data['status'])
              ->setStartDay($data['start_day'])
              ->setExactDay($data['exact_day'])
              ->setEndDay($data['end_day']);

        if (isset($data['store_view'])) {
            $model->setStoreView(implode(',', $data['store_view']));
        } else {
            $model->setStoreView('');
        }
    }
    private function saveAdditionalSettings(&$model, &$data)
    {
        $model->setDiscount($data['discount']);

        if (isset($data['customer_group'])) {
            $model->setCustomerGroup(implode(',', $data['customer_group']));
        } else {
            $model->setCustomerGroup('');
        }
    }

    private function validateDateValues(&$data)
    {
        $datePattern = '/\d{2}\/\d{2}\/\d{4}/';

        foreach ($data as &$value) {
            if (!is_array($value) && preg_match($datePattern, $value)) {
                $date = \DateTime::createFromFormat('d/m/Y', $value);
                if ($date) {
                    $value = $date->format('Y-m-d');
                }
            }
        }
    }
}
