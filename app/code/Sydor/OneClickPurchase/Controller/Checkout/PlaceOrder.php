<?php

namespace Sydor\OneClickPurchase\Controller\Checkout;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json as JsonResult;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Sydor\OneClickPurchase\Service\OrderService;
use Sydor\OneClickPurchase\Service\ProductService;
use Sydor\OneClickPurchase\Service\AddressService;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class PlaceOrder extends Action
{

    private static $knownRequestParams = [
        'form_key' => true,
        'product_id' => true,
        'phone' => true,
        'username' => true,
        'email' => false,
        'qty' => false,
        'super_attribute' => false
    ];

    private $formKeyValidator;
    private $orderService;
    private $addressService;
    private $productService;

    public function __construct(
        Context $context,
        FormKeyValidator $formKeyValidator,
        OrderService $orderService,
        AddressService $addressService,
        ProductService $productService
    ) {
        parent::__construct($context);

        $this->formKeyValidator = $formKeyValidator;
        $this->orderService = $orderService;
        $this->productService = $productService;
        $this->addressService = $addressService;
    }

    public function execute()
    {
        $request = $this->getRequest();
        if (!$this->doesRequestContainAllKnowParams($request)) {
            return $this->createResponse($this->createGenericErrorMessage(), false);
        }
        if (!$this->formKeyValidator->validate($request)) {
            return $this->createResponse($this->createGenericErrorMessage(), false);
        }
        try {
            $quote = $this->orderService->createQuote();
            $quote = $this->productService->setProduct($quote, $request);
            $quote = $this->addressService->setShippingAddress($quote, $request);
            $orderId = $this->orderService->finalizeQuote($quote);
        } catch (NoSuchEntityException $e) {
            return $this->createResponse($this->createGenericErrorMessage(), false);
        } catch (Exception $e) {
            return $this->createResponse(
                $e instanceof LocalizedException ? $e->getMessage() : $this->createGenericErrorMessage(),
                false
            );
        }

        $message = __('Your order number is: %1.', $orderId);

        return $this->createResponse($message, true);
    }

    private function createGenericErrorMessage(): string
    {
        return (string)__('Something went wrong while processing your order. Please try again later.');
    }

    private function doesRequestContainAllKnowParams(RequestInterface $request): bool
    {
        foreach (self::$knownRequestParams as $knownRequestParam => $isRequired) {
            if ($request->getParam($knownRequestParam) === null && $isRequired) {
                return false;
            }
        }

        return true;
    }

    private function getRequestUnknownParams(RequestInterface $request): array
    {
        $requestParams = $request->getParams();
        $unknownParams = [];
        foreach ($requestParams as $param => $value) {
            if (!isset(self::$knownRequestParams[$param])) {
                $unknownParams[$param] = $value;
            }
        }
        return $unknownParams;
    }

    private function createResponse(string $message, bool $successMessage): JsonResult
    {
        /** @var JsonResult $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $result->setData([
            'response' => $message
        ]);
        if ($successMessage) {
            $this->messageManager->addSuccessMessage($message);
        } else {
            $this->messageManager->addErrorMessage($message);
        }

        return $result;
    }
}
