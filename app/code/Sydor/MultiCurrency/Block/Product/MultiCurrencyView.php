<?php

namespace Sydor\MultiCurrency\Block\Product;

use Magento\Framework\View\Element\Template;
use Sydor\MultiCurrency\Helper\Data as CurrencyHelper;
use Magento\Framework\Registry;
use Magento\Catalog\Api\ProductRepositoryInterface;


class MultiCurrencyView extends Template
{
    protected $productRepository;
    protected $_registry;

    protected $price;

    protected $product;
    protected $currencyHelper;

    public function __construct(
        Template\Context $context,
        CurrencyHelper $currencyHelper,
        Registry $registry,
        ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        $this->_registry = $registry;
        $this->currencyHelper = $currencyHelper;
        $this->productRepository = $productRepository;
        parent::__construct($context, $data);
    }

    public function getCurrencyPrice($currency)
    {
        $currencyRate = $this->currencyHelper->getCurrencyRate($currency);

        if (!$currencyRate) {
            return null;
        }
        $productId = $this->getCurrentProduct()->getId();
        $productData = $this->productRepository->getById($productId);
        $productPrice = $productData->getPriceInfo()->getPrice('regular_price')->getValue();


        return intval($productPrice * (float)$currencyRate);
    }

    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }
}
