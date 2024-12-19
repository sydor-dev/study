<?php

namespace Sydor\ProductTabsWidget\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Conditions extends Template implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    protected $_template = 'Sydor_ProductTabsWidget::product/widget/conditions.phtml';

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        if ($element->getRule() && $element->getRule()->getConditions()) {
            $renderedConditions = $element->getRule()->getConditions()->asHtmlRecursive();
            $this->setData('element', $element);
            $this->setData('rendered_conditions', $renderedConditions);

            return $this->toHtml();
        }

        return '';
    }
}
