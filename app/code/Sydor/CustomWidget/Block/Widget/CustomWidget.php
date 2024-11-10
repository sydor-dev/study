<?php

namespace Sydor\CustomWidget\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class CustomWidget extends Template implements BlockInterface
{
    protected $_template = 'Sydor_CustomWidget::widget/customwidget.phtml';

    protected $productCollectionFactory;
    protected $mediaDirectory;

    public function __construct(
        Template\Context $context,
        CollectionFactory $productCollectionFactory,
        DirectoryList $directoryList,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->mediaDirectory = $directoryList->getPath(DirectoryList::MEDIA);
        parent::__construct($context, $data);
    }

    public function getDescription()
    {
        return $this->getData('description');
    }

    public function getProductBySKU()
    {
        $sku = $this->getData('sku');
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['sku', 'name', 'image'])
                   ->addAttributeToFilter('sku', $sku)
                   ->setPageSize(1);

        $product = $collection->getFirstItem();
        return $product->getId() ? $product : null;
    }

    public function getImageUrl($imagePath)
    {
        $imageUrl = $this->_urlBuilder->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'media/catalog/product' . $imagePath;

        return preg_replace('/\/index\.php/', '', $imageUrl);
    }
}
