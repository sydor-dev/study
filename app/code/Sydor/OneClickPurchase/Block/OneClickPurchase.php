<?php

namespace Sydor\OneClickPurchase\Block;

use Magento\Catalog\Model\Product;

class OneClickPurchase extends \Magento\Framework\View\Element\Template
{
    protected $_product;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Product $product,
        array $data = []
    ) {
        $this->_product = $product;
        parent::__construct($context, $data);
    }

    /**
     * Get the current product
     */
    public function getProduct()
    {
        return $this->_product->load($this->getRequest()->getParam('id'));
    }
}
