<?php

namespace Sydor\ProductTabsWidget\Block\Adminhtml\ProductTabsWidget\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\CatalogWidget\Model\Rule\Condition\Combine;
use Sydor\ProductTabsWidget\Block\Conditions as ConditionsBlock;
use Magento\Framework\ObjectManagerInterface;
use Magento\CatalogWidget\Model\Rule;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset as RendererFieldset;

class Form extends Generic
{
    const string DEFAULT_FIELDSET_ID = 'base_fieldset';

    protected $_conditionsTemplate = 'Sydor_ProductTabsWidget::widget/form/renderer/fieldset/element.phtml';

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var RendererFieldset
     */
    protected $_rendererFieldset;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry             $registry,
        \Magento\Framework\Data\FormFactory     $formFactory,
        ObjectManagerInterface                  $objectManager,
        RendererFieldset                        $_rendererFieldset,
        array                                   $data = []
    )
    {
        $this->_objectManager = $objectManager;
        $this->_rendererFieldset = $_rendererFieldset;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/save'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );

        $fieldset = $form->addFieldset(
            self::DEFAULT_FIELDSET_ID,
            ['legend' => __('Tab Information'), 'class' => 'fieldset-wide']
        );

        $data = $this->_coreRegistry->registry('product_tabs_widget_data');
        if ($data && $data->getEntityId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Enable Widget'),
                'id' => 'status',
                'title' => __('Enable Widget'),
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
            'show_pager',
            'select',
            [
                'name' => 'show_pager',
                'label' => __('Show Pager'),
                'title' => __('Show Pager'),
                'required' => false,
                'values' => [
                    1 => __('Yes'),
                    0 => __('No')
                ],
                'value' => $data ? $data->getShowPager() : 0
            ]
        );

        $fieldset->addField(
            'properties',
            'hidden',
            [
                'name' => 'properties',
                'value' => $this->getEncodedProperties()
            ]
        );

        $fieldset->addField(
            'products_per_page',
            'text',
            [
                'name' => 'products_per_page',
                'label' => __('Products Per Page'),
                'title' => __('Products Per Page'),
                'required' => false,
            ]
        );

        $fieldset->addField(
            'products_count',
            'text',
            [
                'name' => 'products_count',
                'label' => __('Products Count'),
                'title' => __('Products Count'),
                'required' => false,
            ]
        );

        $conditionsBlock = $this->_objectManager->create(ConditionsBlock::class);
        $rulesBlock = $this->_objectManager->create(Rule::class);

        if ($data && $data->getConditions()) {
            $conditionsDecoded = json_decode($data->getConditions(), true);
            $conditionsObject = $this->_objectManager->create(Combine::class);
            $conditionsObject->setData($conditionsDecoded);

            if (isset($data['conditions'])) {
                $rulesBlock->loadPost($conditionsDecoded);
            }
        }

        $conditionsBlock->setHtmlId($fieldset->getId())
            ->setNewChildUrl($this->getNewChildUrl());

        $conditionsField = $fieldset->addField(
            'conditions',
            'text',
            [
                'name' => 'conditions',
                'label' => __('Conditions'),
                'title' => __('Conditions'),
                'required' => false
            ]
        )->setRule($rulesBlock)->setRenderer($conditionsBlock);

        $conditionsField->setTemplate($this->_conditionsTemplate);


        if ($data) {
            $form->setValues($data->getData());
        }

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return string
     */
    public function getNewChildUrl()
    {
        return $this->getUrl(
            'catalog_widget/product_widget/conditions/form/' . self::DEFAULT_FIELDSET_ID
        );
    }

    /**
     * @return false|string
     */
    private function getEncodedProperties()
    {
        return json_encode(
            [
                'page_var_name' => $this->getRandomPaveVar()
            ]
        );
    }

    /**
     * @return string
     * @throws \Random\RandomException
     */
    private function getRandomPaveVar()
    {
        $randomString = bin2hex(random_bytes(3));
        return 'p' . substr($randomString, 0, 5);
    }
}
