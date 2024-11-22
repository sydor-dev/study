<?php

namespace LiqpayMagento\LiqPay\Controller\Callback;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RedirectFactory;

class Index extends Action
{

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    public function __construct(
        Context $context,
        RedirectFactory $resultRedirectFactory,
    ) {
        parent::__construct($context);
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    public function execute()
    {
        return $this->resultRedirectFactory->create()->setPath('checkout/onepage/success');
    }
}
