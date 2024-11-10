<?php

namespace Sydor\OneClickPurchase\Service;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;

class ProductService
{

    private $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function setProduct($quote, $request)
    {
        $product = $this->productRepository->getById($request->getParam('product_id'));
        $productAttributes = $request->getParam('super_attribute');

        $productOptions = [
            'product' => $product->getId(),
            'qty' => !empty($request->getParam('qty')) ? $request->getParam('qty') : 1,
        ];

        if ($product->getTypeId() === 'configurable'){
            if (empty($productAttributes)) {
                throw new LocalizedException(__('You have to specify product options.'));
            }
            foreach ($productAttributes as $attributeId => $optionId) {
                $productOptions['super_attribute'][$attributeId] = $optionId;
            }
        }

        $addResult = $quote->addProduct($product, new DataObject($productOptions));

        if (!$addResult instanceof \Magento\Quote\Model\Quote\Item) {
            throw new LocalizedException(__('We can\'t add this item to your shopping cart right now.'));
        }

        return $quote;
    }
}
