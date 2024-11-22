<?php
/**
 * FormUrlencoded deserializer of REST request content.
 */
namespace LiqpayMagento\LiqPay\Webapi\Rest\Request\Deserializer;

use InvalidArgumentException;
use Magento\Framework\App\State;
use Magento\Framework\Phrase;
use Magento\Framework\Stdlib\Parameters;
use Magento\Framework\Webapi\Rest\Request\DeserializerInterface;

/**
 * Class FormUrlencoded
 */
class FormUrlencoded implements DeserializerInterface
{
    /**
     * @var Parameters
     */
    private $parameters;
    /**
     * @var State
     */
    protected $_appState;
    /**
     * FormUrlencoded constructor.
     * @param Parameters $parameters
     * @param State $appState
     */
    public function __construct(
        Parameters $parameters,
        State $appState
    ) {
        $this->parameters = $parameters;
        $this->_appState = $appState;
    }

    /**
     * Parse Request body into array of params.
     *
     * @param string $encodedBody Posted content from request.
     * @return array|null Return NULL if content is invalid.
     * @throws InvalidArgumentException
     * @throws \Magento\Framework\Webapi\Exception If decoding error was encountered.
     */
    public function deserialize($encodedBody)
    {
        if (!is_string($encodedBody)) {
            throw new \InvalidArgumentException(
                sprintf('"%s" data type is invalid. String is expected.', gettype($encodedBody))
            );
        }
        try {
            $decodedBody = urldecode($encodedBody);
            $this->parameters->fromString($decodedBody);
            return $this->parameters->toArray();
        } catch (\InvalidArgumentException $e) {
            if ($this->_appState->getMode() !== State::MODE_DEVELOPER) {
                throw new \Magento\Framework\Webapi\Exception(new Phrase('Decoding error.'));
            } else {
                throw new \Magento\Framework\Webapi\Exception(
                    new Phrase(
                        'Decoding error: %1%2%3%4',
                        [PHP_EOL, $e->getMessage(), PHP_EOL, $e->getTraceAsString()]
                    )
                );
            }
        }
    }
}
