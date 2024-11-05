<?php

namespace Sydor\Holidays\Helper;

use Magento\Customer\Model\Session;
use Sydor\Holidays\Model\ResourceModel\Holidays\CollectionFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;

class Helper extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var PricingHelper
     */
    public $pricingHelper;

    /**
     * @var Session
     */
    public $customerSession;

    /**
     * @var CollectionFactory
     */
    public $collectionFactory;

    public function __construct(
        Session $customerSession,
        CollectionFactory $collectionFactory,
        PricingHelper $pricingHelper,
        Context $context
    )
    {
        $this->pricingHelper = $pricingHelper;
        $this->customerSession = $customerSession;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }


    public function getCustomerId()
    {
        return $this->customerSession->getCustomerId();
    }

    public function getCustomerGroupId()
    {
        return $this->customerSession->getCustomerGroupId();
    }


    public function getPriceWithCurrency($price)
    {
        return $this->pricingHelper->currency($price, true, false);
    }
    public function getMaxAvailableDiscount()
    {

        $generalCollection = $this->createGeneralCollection();
        $allcustomersCollection = $this->getAllCustomersCollection( clone $generalCollection);
        $allCustomersMaxDiscount = $allcustomersCollection->getFirstItem()->getDiscount();

        $customerGroupMaxDiscount = null;
        if ($customerId = $this->getCustomerGroupId()) {
            $customerGroupCollection = $this->getCustomerGroupCollection($customerId, clone $generalCollection);
            $customerGroupMaxDiscount = $customerGroupCollection->getFirstItem()->getDiscount();
        }

        if ($customerGroupMaxDiscount !== null) {
            return max($allCustomersMaxDiscount, $customerGroupMaxDiscount);
        }

        $maxDiscount = !empty($allCustomersMaxDiscount) ? $allCustomersMaxDiscount : null;

        return $maxDiscount;
    }
    public function getCustomerGroupCollection($customerId, $collection)
    {
        $collection->addFieldToFilter('customer_group', ['like' => '%' . $customerId . '%']);

        return $collection;
    }

    public function getAllCustomersCollection($collection)
    {
        $collection->addFieldToFilter('customer_group', ['eq' => 0]);

        return $collection;
    }

    private function createGeneralCollection()
    {
        $currentDate = date('Y-m-d');
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('status', 1);
        $collection->addFieldToFilter('start_day', ['lt' => $currentDate]);
        $collection->addFieldToFilter('end_day', ['gt' => $currentDate]);
        $collection->getSelect()->order('discount DESC')->limit(1);

        return $collection;
    }

    public function getDiscountedPrice($product)
    {
        if (!($product instanceof \Magento\Catalog\Model\Product)) {
            return false;
        }

        $productPrice = $product->getFinalPrice();
        $discount = (int) $this->getMaxAvailableDiscount();

        if ($productPrice && $discount) {
            $discountedPrice = $productPrice - ($productPrice * ($discount / 100));

            return $discountedPrice;
        }

        return $productPrice;
    }
}
