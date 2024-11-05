<?php
namespace Sydor\InventoryFulfillment\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Sydor\InventoryFulfillment\Api\ProductManagementInterface;

class ProductManagement implements ProductManagementInterface
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getProductBySku($sku)
    {
        try {
            return $this->productRepository->get($sku);
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__('Product with SKU "%1" does not exist.', $sku));
        }
    }
}
