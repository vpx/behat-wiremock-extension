<?php
/**
 * @author Vitalii Piskovyi <vitalii.piskovyi@gmail.com>
 */
namespace VPX\WiremockExtension\Context;

use VPX\WiremockExtension\ServiceContainer\Wiremock;

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
