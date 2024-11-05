<?php
namespace Sydor\InventoryFulfillment\Api;

interface ProductManagementInterface
{
    /**
     * Get product by SKU.
     *
     * @param string $sku
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductBySku($sku);
}
