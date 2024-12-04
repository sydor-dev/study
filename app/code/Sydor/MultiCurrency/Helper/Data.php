<?php

namespace Sydor\MultiCurrency\Helper;

use Magento\Directory\Model\CurrencyFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Encryption\EncryptorInterface;

class Data extends AbstractHelper
{
    protected $product;
    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @param Context $context
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        Context $context,
        EncryptorInterface $encryptor,
        CurrencyFactory $currencyFactory,
        StoreManagerInterface $storeManager,
    )
    {
        parent::__construct($context);
        $this->encryptor = $encryptor;
        $this->currencyFactory = $currencyFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $currency Allowed values 'uah' or 'euro'
     * @return float|null
     */
    public function getCurrencyRate($currencyCode = 'uah') {

        if ($this->useSystemValues()) {
            $currentCurrencyCode =  $this->storeManager->getStore()->getCurrentCurrency()->getCode();
            $currencyRate = $this->currencyFactory->create()->load($currentCurrencyCode)->getAnyRate($currencyCode);
        } else {
            $currencyRate = $this->scopeConfig->getValue(
                "multicurrency/general/{$currencyCode}_currency_value"
            );
        }

        return $currencyRate ?? null;
    }

    /**
     * @return bool
     */
    public function useSystemValues() {
        return $this->scopeConfig->isSetFlag(
            'multicurrency/general/use_system_values',
        );
    }

}
