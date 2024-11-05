<?php

namespace Sydor\Holidays\Block\Adminhtml\Holidays\Edit\Tab;

class Additional extends \Magento\Backend\Block\Widget\Form\Generic
{
    private $dateFields = [];

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customerGroupCollectionFactory,
        array $data = []
    )
    {
        $this->customerGroupCollectionFactory = $customerGroupCollectionFactory;
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
            'discount',
            'text',
            [
                'name' => 'discount',
                'label' => __('Discount'),
                'id' => 'discount',
                'title' => __('Discount'),
                'class' => 'validate-number validate-digits-range digits-range-0-100',
                'required' => false,
                'note' => __('Enter the discount percentage (e.g., 10 for 10%)'),
            ]
        );

        $fieldset->addField(
            'customer_group',
            'multiselect',
            [
                'name' => 'customer_group',
                'label' => __('Customer Group'),
                'values' => $this->getCustomerGroupOptions(),
                'id' => 'customer-group',
                'title' => __('Customer Group'),
                'required' => false,
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    private function getCustomerGroupOptions()
    {
        $options = [];

        $options[] = [
            'value' => 0,
            'label' => __('All Customer Groups'),
        ];

        $customerGroups = $this->customerGroupCollectionFactory->create()->toOptionArray();
        foreach ($customerGroups as $group) {
            $options[] = [
                'value' => $group['value'],
                'label' => $group['label'],
            ];
        }

        return $options;
    }
}
