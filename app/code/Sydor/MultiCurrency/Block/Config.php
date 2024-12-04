<?php

namespace Sydor\MultiCurrency\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Sydor\MultiCurrency\Helper\Data;

class Config extends Template
{
    /**
     * @var Data
     */
    protected $helper;

    public function __construct(Context $context, Data $helper) {
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function getUahCurrency()
    {
        return $this->helper->getUahCurrency();
    }
}
