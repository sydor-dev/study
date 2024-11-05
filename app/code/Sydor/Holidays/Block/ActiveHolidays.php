<?php

namespace Sydor\Holidays\Block;

use Magento\Framework\View\Element\Template;
use Sydor\Holidays\Helper\Helper;

class ActiveHolidays extends Template
{

    private $helper;

    private $collectionFactory;

    private $activeHolidays;


    public function __construct(
        Template\Context $context,
        Helper $helper,
        array $data = []
    )
    {
        $this->helper = $helper;
        $this->collectionFactory = $helper->collectionFactory;
        parent::__construct($context, $data);
    }

    public function getActiveHolidays()
    {
        $currentDate = date('Y-m-d');

        if (!$this->activeHolidays) {
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('status', 1);
            $collection->setOrder('start_day', 'DESC');
            $collection->addFieldToFilter('start_day', ['lt' => $currentDate]);
            $collection->addFieldToFilter('end_day', ['gt' => $currentDate]);
            $this->activeHolidays = $collection;
        }

        return $this->activeHolidays;
    }
}

