<?php
/**
 * @author Vitalii Piskovyi <vitalii.piskovyi@gmail.com>
 */
namespace VPX\WiremockExtension\Context;

use VPX\WiremockExtension\ServiceContainer\Wiremock;

interface WiremockAwareContextInterface
{
    /**
     * @param Wiremock $wiremock
     */
    public function setWiremock(Wiremock $wiremock);

    /**
     * @return Wiremock
     */
    public function getWiremock(): Wiremock;
}
