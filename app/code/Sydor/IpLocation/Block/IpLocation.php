<?php

namespace Sydor\IpLocation\Block;

use Magento\Framework\View\Element\Template;
use Sydor\IpLocation\Api\Data\IpLocationInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

class IpLocation extends Template
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var RemoteAddress
     */
    protected $remoteAddress;

    /**
     * Конструктор блока.
     *
     * @param Template\Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param RemoteAddress $remoteAddress
     * @param array $data
     */
    public function __construct(
        Template\Context     $context,
        ScopeConfigInterface $scopeConfig,
        RemoteAddress        $remoteAddress,
        array                $data = []
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->remoteAddress = $remoteAddress;
        parent::__construct($context, $data);
    }

    /**
     * @return string|null
     */
    public function getUserIp(): ?string
    {
        return $this->remoteAddress->getRemoteAddress();
    }

    /**
     * @return string
     */
    public function getIpLocationApiKey(): string
    {
        return $this->scopeConfig->getValue(IpLocationInterface::XML_PATH_API_KEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
