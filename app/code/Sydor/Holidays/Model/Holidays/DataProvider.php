<?php

namespace Sydor\Holidays\Model\Holidays;

use Magento\Framework\Data\OptionSourceInterface;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    protected $loadedData;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Sydor\Holidays\Model\ResourceModel\Holidays\CollectionFactory $holidaysCollectionFactory,
        array $meta = [],
        array $data = []
    )
    {
        $this->collection = $holidaysCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $holidays) {
            $this->loadedData[$holidays->getId()] = $holidays->getData();
        }

        return $this->loadedData;
    }
}
