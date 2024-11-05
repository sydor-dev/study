<?php

namespace Sydor\Holidays\Plugin;

use Sydor\Holidays\Helper\Helper;
use Magento\Catalog\Model\Product;

class ProductPricePlugin
{
    /**
     * @var Helper
     */
    public $helper;

    public function __construct(
        Helper $helper
    )
    {
        $this->helper = $helper;
    }

    public function aroundGetFinalPrice(Product $subject, callable $proceed, $qty = null)
    {
        $finalPrice = $proceed($qty);

        $maxHolidaysDiscount = $this->helper->getMaxAvailableDiscount();
        $productDiscount = (int)$subject->getData('max_available_discount');
        $maxAvailableDiscount = max($productDiscount, $maxHolidaysDiscount);

        if (!empty($maxAvailableDiscount)) {
            $finalPrice -= $finalPrice * ($maxAvailableDiscount / 100);
        }

        return $finalPrice;
    }
}
