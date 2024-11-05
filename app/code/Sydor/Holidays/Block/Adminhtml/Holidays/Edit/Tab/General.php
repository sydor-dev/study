<?php

namespace Sydor\Holidays\Block\Adminhtml\Holidays\Edit\Tab;

class General extends \Magento\Backend\Block\Widget\Form\Generic
{
    private $dateFields = [];

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    )
    {

        $this->dateFields = [
            'start_day' => __('Holiday Start Day'),
            'exact_day' => __('Holiday Exact Day'),
            'end_day' => __('Holiday End Day')
        ];
        $this->storeManager = $storeManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {

        $model = $this->_coreRegistry->registry('holidays_data');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('holidays_');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Edit Holidays'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'id' => 'title',
                'title' => __('Title'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
        $fieldset->addField(
            'content',
            'textarea',
            [
                'name' => 'content',
                'label' => __('Content'),
                'id' => 'content',
                'title' => __('Content'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Enable Holiday'),
                'id' => 'status',
                'title' => __('Enable Holiday'),
                'class' => 'required-entry',
                'required' => true,
                'values' => [
                    1 => __('Yes'),
                    0 => __('No')
                ],
                'value' => 1
            ]
        );
        $fieldset->addField(
            'store_view',
            'multiselect',
            [
                'name' => 'store_view',
                'label' => __('Store View'),
                'values' => $this->getStoreViewOptions(),
                'id' => 'store-view',
                'title' => __('Store View'),
                'required' => true,
            ]
        );

        foreach ($this->dateFields as $fieldName => $fieldTitle) {
            $fieldset->addField(
                $fieldName,
                'date',
                [
                    'name' => $fieldName,
                    'label' => $fieldTitle,
                    'values' => $this->getStoreViewOptions(),
                    'id' => str_replace('_', '-', $fieldName),
                    'title' => $fieldTitle,
                    'required' => true,
                    'format' => 'dd/MM/yyyy',
                    'time' => false,
                    'input_format' => \IntlDateFormatter::SHORT,
                    'date_format' => 'dd/MM/yyyy',
                    'class' => 'validate-date validate-date-range date-range-holiday'
                ]
            );
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    private function getStoreViewOptions()
    {
        $options = [];

        $options[] = [
            'value' => 0,
            'label' => __('All Store Views'),
        ];

        $stores = $this->storeManager->getStores();
        foreach ($stores as $store) {
            $options[] = [
                'value' => $store->getId(),
                'label' => $store->getName(),
            ];
        }

        return $options;
    }
}
