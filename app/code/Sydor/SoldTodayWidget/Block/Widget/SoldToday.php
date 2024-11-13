<?php

namespace Sydor\SoldTodayWidget\Block\Widget;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Catalog\Block\Product\View as ProductView;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\View\Element\Block\ArgumentInterfaceFactory;

class SoldToday extends Template
{
    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var ArgumentInterfaceFactory
     */
    protected $viewModelFactory;

    /**
     * @var string
     */
    protected $_template = 'Sydor_SoldTodayWidget::widget/sold-today.phtml';

    /**
     * @var string
     */
    protected $productListTemplate = 'Magento_Catalog::product/list.phtml';

    /**
     * @var int
     */
    private const LIMIT = 5;

    /**
     * @param Template\Context $context
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param ProductRepositoryInterface $productRepository
     * @param DateTime $dateTime
     * @param ProductView $productView
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ArgumentInterfaceFactory $viewModelFactory
     * @param array $data
     */
    public function __construct(
        Template\Context            $context,
        OrderCollectionFactory      $orderCollectionFactory,
        ProductRepositoryInterface  $productRepository,
        DateTime                    $dateTime,
        ProductView                 $productView,
        CategoryRepositoryInterface $categoryRepository,
        ArgumentInterfaceFactory    $viewModelFactory,
        array                       $data = []
    )
    {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->productRepository = $productRepository;
        $this->dateTime = $dateTime;
        $this->productVied = $productView;
        $this->categoryRepository = $categoryRepository;
        $this->viewModelFactory = $viewModelFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getSoldTodayProducts(array $categoryIds)
    {
        $todayStart = $this->dateTime->gmtDate('Y-m-d 00:00:00', strtotime('today'));
        $todayEnd = $this->dateTime->gmtDate('Y-m-d 23:59:59', strtotime('today'));

        $orderCollection = $this->orderCollectionFactory->create()
            ->addFieldToFilter('created_at', ['from' => $todayStart, 'to' => $todayEnd]);

        $soldProducts = [];
        foreach ($orderCollection as $order) {
            foreach ($order->getAllVisibleItems() as $item) {
                $soldProduct = $this->productRepository->getById($item->getProductId());
                if (array_intersect($categoryIds, $soldProduct->getCategoryIds())) {
                    $soldProducts[] = $soldProduct;
                }
            }
        }

        return $soldProducts;
    }

    /**
     * Current product category ids
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getProductCategoryIds()
    {
        $productId = $this->getRequest()->getParam('id');
        $product = $this->productRepository->getById($productId);
        $categoryIds = $product->getCategoryIds();

        if (empty($categoryIds)) {
            return [];
        }

        return $categoryIds;
    }

    /**
     * @return false|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductListHtml()
    {
        $productCategoryIds = $this->getProductCategoryIds();
        $soldTodayProducts = $this->getSoldTodayProducts($productCategoryIds);

        if (empty($soldTodayProducts)) {
            return false;
        }

        try {
            $category = $this->categoryRepository->get($productCategoryIds[0]);
            $productCollection = $this->getProductCollection($category, $soldTodayProducts);
            $toolbarBlock = $this->getToolbarBlock($category, $productCollection);

            return $this->getProductListBlock($category, $productCollection, $toolbarBlock)->toHtml();
        } catch (\Exception $e) {
            return __('Error: %1', $e->getMessage());
        }
    }

    /**
     * @param $category
     * @param array $soldTodayProducts
     * @return \Magento\Catalog\Model\Category
     */
    private function getProductCollection($category, array $soldTodayProducts)
    {
        $soldProductIds = array_map(function ($product) {
            return $product->getId();
        }, $soldTodayProducts);

        return $category->getProductCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', ['in' => $soldProductIds])
            ->setPageSize(self::LIMIT)
            ->load();
    }

    /**
     * @param $category
     * @param $productCollection
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getToolbarBlock($category, $productCollection)
    {
        return $this->getLayout()->createBlock('Magento\Catalog\Block\Product\ProductList\Toolbar')
            ->setCategory($category)
            ->setCollection($productCollection)
            ->setTemplate('Magento_Catalog::product/list/toolbar.phtml');
    }

    /**
     * @param $category
     * @param $productCollection
     * @param $toolbarBlock
     * @return \Magento\Catalog\Block\Product\ListProduct
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getProductListBlock($category, $productCollection, $toolbarBlock)
    {
        return $this->getLayout()->createBlock('Magento\Catalog\Block\Product\ListProduct')
            ->setCategory($category)
            ->setCollection($productCollection)
            ->setChild('toolbar', $toolbarBlock)
            ->setTemplate($this->productListTemplate)
            ->setData('viewModel', (new \Magento\Catalog\ViewModel\Product\OptionsData()));
    }

    /**
     * @return bool
     */
    public function isWidgetEnabled()
    {
        return $this->getData('enabled') == '1';
    }
}
