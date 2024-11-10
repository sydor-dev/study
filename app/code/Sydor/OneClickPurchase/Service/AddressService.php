<?php

namespace Sydor\OneClickPurchase\Service;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerFactory;

class AddressService
{
    private const INITIAL_SHIPPING_METHOD = 'flatrate_flatrate';


    protected $customerFactory;


    protected $customerRepository;


    public function __construct(
        CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository,
    ) {
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
    }

    private function getShippingAddress($request)
    {
        $email = $request->getParam('email');
        $shippingAddress = [
            'telephone' => $request->getParam('phone'),
            'email' => !empty($email) ? $email : 'guest@example.com',
            'firstname' => $this->getUserFirstName($request->getParam('username')),
            'lastname' => $this->getUserLastName($request->getParam('username')),
            'save_in_address_book' => 0
        ];

        if (!empty($email) && $customer = $this->isCustomerExistsByEmail($email)) {
            if ($customerShippingAddress = $customer->getDefaultShippingAddress()) {
                $shippingAddress = array_merge($shippingAddress, $customerShippingAddress->getData());
            } else {
                $shippingAddress = array_merge($shippingAddress, $this->getInitialShippingAddress());
            }
        } else {
            $shippingAddress = array_merge($shippingAddress, $this->getInitialShippingAddress());
        }

        return $shippingAddress;
    }

    public function setShippingAddress($quote, $request)
    {
        $shippingAddress = $this->getShippingAddress($request);
        $quote->getShippingAddress()->addData($shippingAddress)
            ->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod(self::INITIAL_SHIPPING_METHOD);
        $quote->getBillingAddress()->addData($shippingAddress);

        return $quote;
    }

    private function isCustomerExistsByEmail($email)
    {
        $customer = $this->customerFactory->create();
        $customer->loadByEmail($email);

        return $customer->getId() ? $customer : null;
    }


    private function getUserFirstName($username)
    {
        $nameParts = explode(' ', $username);
        return $nameParts[0] ?? '';
    }


    private function getUserLastName($username)
    {
        $nameParts = explode(' ', $username);
        return isset($nameParts[1]) ? $nameParts[1] : $username;
    }


    private function getInitialShippingAddress()
    {
        return [
            'street' => '123 Default Street',
            'city' => 'Default City',
            'region_id' => '1',
            'region' => 'Default Region',
            'postcode' => '000001',
            'country_id' => 'US'
        ];
    }
}
