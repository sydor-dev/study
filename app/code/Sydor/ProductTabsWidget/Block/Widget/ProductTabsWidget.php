<?php

namespace Sydor\ProductTabsWidget\Block\Widget;

use Magento\Catalog\Block\Product\Widget\Html\Pager;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Widget\Helper\Conditions;
use Sydor\ProductTabsWidget\Model\ProductTabsWidgetFactory;
use Magento\CatalogWidget\Block\Product\ProductsList;
use Magento\CatalogWidget\Model\Rule;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Rule\Model\Condition\Sql\Builder as SqlBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;

class ProductTabsWidget extends AbstractProduct
{
    const int DEFAULT_PRODUCTS_PER_PAGE = 4;
    const int DEFAULT_PRODUCTS_COUNT = 8;
    const int MAX_TABS_AMOUNT = 3;
    const bool DEFAULT_SHOW_PAGER = false;
    const string WIDGET_PAGER_BLOCK_NAME = 'widget.products.list.pager';

    protected $_template = 'Sydor_ProductTabsWidget::widget/product-tabs-widget.phtml';

    /**
     * @var Rule
     */
    protected $rule;
    /**
     * @var ProductTabsWidgetFactory
     */
    private $widgetsFactory;

    /**
     * @var ProductsList
     */
    public $productList;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $conditionsHelper;
    /**
     * @var Conditions
     */
    protected $categoryRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var SqlBuilder
     */
    protected $sqlBuilder;

    /**
     * @var Pager
     */
    protected $pager;

    public function __construct(
        Pager                       $pager,
        ProductTabsWidgetFactory    $widgetsFactory,
        ProductsList                $productList,
        Rule                        $rule,
        Conditions                  $conditionsHelper,
        CategoryRepositoryInterface $categoryRepository = null,
        StoreManagerInterface       $storeManager,
        ProductCollectionFactory    $productCollectionFactory,
        SqlBuilder                  $sqlBuilder,
        Context                     $context,
        array                       $data = []

    )
    {
        $this->pager = $pager;
        $this->widgetsFactory = $widgetsFactory;
        $this->productList = $productList;
        $this->rule = $rule;
        $this->conditionsHelper = $conditionsHelper;
        $this->categoryRepository = $categoryRepository ?? ObjectManager::getInstance()
            ->get(CategoryRepositoryInterface::class);
        $this->_storeManager = $storeManager;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->sqlBuilder = $sqlBuilder;
        parent::__construct($context, $data);
    }

    /**
     * @return Collection | null
     */
    public function getActiveTabs()
    {
        $this->setStoreId($this->_storeManager->getStore()->getId());

        $collection = $this->widgetsFactory->create()->getCollection()
            ->addFieldToFilter('status', ['eq' => 1])
            ->setOrder('entity_id', 'ASC')
            ->setPageSize(self::MAX_TABS_AMOUNT);

        return $collection;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $activeTabData
     * @return void
     */
    private function setActiveTabData(\Magento\Framework\Model\AbstractModel $activeTabData)
    {
        foreach ($activeTabData->getData() as $key => $value) {
            $this->setData($key, $value);
        }
    }

    /**
     * @return array|mixed|null
     */
    public function getStatus()
    {
        return $this->getData('status');
    }

    /**
     * @param $activeTabData
     * @return \Magento\Rule\Model\Condition\Combine
     */
    private function getConditions($activeTabData)
    {
        $conditions = $activeTabData->getData('conditions');

        if (is_string($conditions)) {
            $conditions = $this->decodeConditions($conditions);
        }

        if (isset($conditions['conditions'])) {
            $conditions = reset($conditions);
        }

        foreach ($conditions as $key => $condition) {
            if (!empty($condition['attribute'])) {
                if (in_array($condition['attribute'], ['special_from_date', 'special_to_date'])) {
                    $conditions[$key]['value'] = date('Y-m-d H:i:s', strtotime($condition['value']));
                }

                if ($condition['attribute'] == 'category_ids') {
                    $conditions[$key] = $this->updateAnchorCategoryConditions($condition);
                }
            }
        }

        $this->rule->loadPost(['conditions' => $conditions]);
        return $this->rule->getConditions();
    }

    /**
     * @param array $condition
     * @return array
     */
    private function updateAnchorCategoryConditions(array $condition): array
    {
        if (array_key_exists('value', $condition)) {
            $categoryId = $condition['value'];

            try {
                $category = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
            } catch (NoSuchEntityException $e) {
                return $condition;
            }

            $children = $category->getIsAnchor() ? $category->getChildren(true) : [];
            if ($children) {
                $children = explode(',', $children);
                $condition['operator'] = "()";
                $condition['value'] = array_merge([$categoryId], $children);
            }
        }

        return $condition;
    }

    /**
     * @param string $encodedConditions
     * @return array
     */
    private function decodeConditions(string $encodedConditions): array
    {
        return $this->conditionsHelper->decode(htmlspecialchars_decode($encodedConditions));
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $activeTabData
     * @return ProductCollection
     * @throws LocalizedException
     */
    public function getActiveTabCollection(\Magento\Framework\Model\AbstractModel $activeTabData): ProductCollection
    {
        $this->setActiveTabData($activeTabData);
        $this->pager = '';

        $collection = $this->productCollectionFactory->create();

        if ($this->getStoreId() !== null && !empty($this->getData('store_ids'))) {
            $availableStoreIds = explode(',', trim($this->getData('store_ids')));

            if (in_array($this->getStoreId(), $availableStoreIds)) {
                $collection->setStoreId($this->getStoreId());
            }
        }

        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addAttributeToSort('entity_id', 'desc')
            ->setPageSize($this->getPageSize($activeTabData));

        $conditions = $this->getConditions($activeTabData);
        $conditions->collectValidatedAttributes($collection);
        $this->sqlBuilder->attachConditionToCollection($collection, $conditions);
        $collection->distinct(true);
        $this->setProductCollection($collection);

        return $collection;
    }

    /**
     * @param $activeTabData
     * @return int
     */
    private function getPageSize($activeTabData)
    {
        return $activeTabData->getProductsPerPage() ?: self::DEFAULT_PRODUCTS_PER_PAGE;
    }

    /**
     * @return bool
     */
    private function showPager()
    {
        if (!$this->hasData('show_pager')) {
            $this->setData('show_pager', self::DEFAULT_SHOW_PAGER);
        }

        return (bool)$this->getData('show_pager');
    }

    public function getProductsPerPage()
    {
        if (!$this->hasData('products_per_page')) {
            $this->setData('products_per_page', self::DEFAULT_PRODUCTS_PER_PAGE);
        }
        return (int)$this->getData('products_per_page');
    }


    /**
     * @return int
     */
    private function getProductsCount()
    {
        if ($this->hasData('products_count')) {
            return (int)$this->getData('products_count');
        }

        if (null === $this->getData('products_count')) {
            $this->setData('products_count', self::DEFAULT_PRODUCTS_COUNT);
        }

        return (int)$this->getData('products_count');
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getPagerHtml()
    {
        if ($this->isSliderMode()) {
            return '';
        }

        if ($this->showPager() && $this->getProductCollection()->getSize() > $this->getProductsPerPage()) {
            if (!$this->pager) {
                $this->pager = $this->getLayout()->createBlock(
                    Pager::class,
                    self::WIDGET_PAGER_BLOCK_NAME
                );

                $this->pager->setUseContainer(true)
                    ->setShowAmounts(true)
                    ->setShowPerPage(false)
                    ->setPageVarName($this->getPageVarName())
                    ->setLimit($this->getProductsPerPage())
                    ->setTotalLimit($this->getProductsCount())
                    ->setCollection($this->getProductCollection());
            }

            if ($this->pager instanceof \Magento\Framework\View\Element\AbstractBlock) {
                return $this->pager->toHtml();
            }
        }
        return '';
    }

    /**
     *
     * @return bool
     * @note Temporary hardcoded
     */
    public function isSliderMode()
    {
        return true;
    }
}
