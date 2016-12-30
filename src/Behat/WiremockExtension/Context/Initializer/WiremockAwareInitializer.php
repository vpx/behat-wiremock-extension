<?php
/**
 * @author Vitalii Piskovyi <vitalii.piskovyi@gmail.com>
 */
namespace Behat\WiremockExtension\Context\Initializer;

use Behat\Behat\Context\Context as ContextInterface;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Behat\WiremockExtension\Context\WiremockAwareContextInterface;
use Behat\WiremockExtension\ServiceContainer\Wiremock;

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
