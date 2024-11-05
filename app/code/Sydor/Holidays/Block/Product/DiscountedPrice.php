<?php

namespace Sydor\Holidays\Block\Product;

use Magento\Catalog\Block\Product\View as ProductView;
use Sydor\Holidays\Helper\Helper;
use Magento\Framework\View\Element\Template;

class DiscountedPrice extends Template
{
    /**
     * @var Helper
     */
    public $helper;

    /**
     * @var ProductView
     */
    private $productView;

    public function __construct(
        Template\Context $context,
        Helper $helper,
        ProductView $productView,
        array $data = []
    )
    {
        $this->helper = $helper;
        $this->productView = $productView;
        parent::__construct($context, $data);
    }

    public function getDiscountedPrice()
    {
        $finalPrice = $this->getCurrentProduct()->getFinalPrice();

        return $this->helper->getPriceWithCurrency($finalPrice);
    }
    public function getCurrentProduct()
    {
        return $this->productView->getProduct();
    }

}
