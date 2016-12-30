<?php
/**
 * @author Vitalii Piskovyi <vitalii.piskovyi@gmail.com>
 */
namespace Behat\WiremockExtension\Context;

use Behat\WiremockExtension\ServiceContainer\Wiremock;

class WiremockAwareContext implements WiremockAwareContextInterface
{
    /**
     * @var Wiremock
     */
    private $wiremock;

    /**
     * {@inheritdoc}
     */
    public function setWiremock(Wiremock $wiremock)
    {
        $this->wiremock = $wiremock;
    }

    /**
     * {@inheritdoc}
     */
    public function getWiremock(): Wiremock
    {
       return $this->wiremock;
    }
}
