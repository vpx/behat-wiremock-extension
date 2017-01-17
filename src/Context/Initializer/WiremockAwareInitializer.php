<?php
/**
 * @author Vitalii Piskovyi <vitalii.piskovyi@gmail.com>
 */
namespace VPX\WiremockExtension\Context\Initializer;

use Behat\Behat\Context\Context as ContextInterface;
use Behat\Behat\Context\Initializer\ContextInitializer;
use VPX\WiremockExtension\Context\WiremockAwareContextInterface;
use VPX\WiremockExtension\ServiceContainer\Wiremock;

class WiremockAwareInitializer implements ContextInitializer
{
    /**
     * @var Wiremock
     */
    private $wiremock;

    /**
     * @param Wiremock $wiremock
     */
    public function __construct(Wiremock $wiremock)
    {
        $this->wiremock = $wiremock;
    }

    /**
     * {@inheritdoc}
     */
    public function initializeContext(ContextInterface $context)
    {
        if ($context instanceof WiremockAwareContextInterface) {
            $context->setWiremock($this->wiremock);
        }
    }
}
