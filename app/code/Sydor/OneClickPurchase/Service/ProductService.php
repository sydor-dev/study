<?php

namespace Sydor\OneClickPurchase\Service;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\Quote\Model\Quote;

class ProductService
{

    private $productRepository;
    private $configurableType;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ConfigurableType $configurableType
)
    {
        $this->productRepository = $productRepository;
        $this->configurableType = $configurableType;
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

            $this->getRequiredAttributes($product);

            foreach ($this->getRequiredAttributes($product) as $attributeId => $attributeLabel) {
                if (!isset($productAttributes[$attributeId])) {
                    throw new LocalizedException(__('You have to specify product option: %1.', $attributeLabel));
                }

                $productOptions['super_attribute'][$attributeId] = $productAttributes[$attributeId];
            }
        }

        $addResult = $quote->addProduct($product, new DataObject($productOptions));

        if (!$addResult instanceof \Magento\Quote\Model\Quote\Item) {
            throw new LocalizedException(__('We can\'t add this item to your shopping cart right now.'));
        }

        return $quote;
    }

    public function getRequiredAttributes($product)
    {
        if ($product->getTypeId() !== 'configurable') {
            throw new LocalizedException(__('This product is not configurable.'));
        }

        /** @var ConfigurableType $configurableType */
        $configurableType = $this->configurableType;
        $attributeIds = $configurableType->getConfigurableAttributesAsArray($product);

        $requiredAttributes = [];
        foreach ($attributeIds as $attribute) {
            $requiredAttributes[$attribute['attribute_id']] = $attribute['label'];
        }

        return $requiredAttributes;
    }
}
