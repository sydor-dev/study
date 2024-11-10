<?php

namespace Sydor\OneClickPurchase\Service;

use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;

class OrderService
{
    private const INITIAL_PAYMENT_METHOD = 'checkmo';
    private $cartManagement;
    private $cartRepository;
    private $productRepository;

    public function __construct(
        CartManagementInterface    $cartManagement,
        CartRepositoryInterface    $cartRepository,
        ProductRepositoryInterface $productRepository
    )
    {
        $this->cartManagement = $cartManagement;
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
    }

    public function createQuote()
    {
        $quoteId = $this->cartManagement->createEmptyCart();
        $quote = $this->cartRepository->get($quoteId);

        return $quote;
    }

    public function finalizeQuote($quote)
    {
        $quote->setPaymentMethod(self::INITIAL_PAYMENT_METHOD);
        $quote->getPayment()->importData(['method' => self::INITIAL_PAYMENT_METHOD]);
        $quote->setInventoryProcessed(false);
        $quote->collectTotals();
        $this->cartRepository->save($quote);

        return $this->cartManagement->placeOrder($quote->getId());
    }
}
