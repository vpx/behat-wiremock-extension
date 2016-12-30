<?php
/**
 * @author Vitalii Piskovyi <vitalii.piskovyi@gmail.com>
 */
namespace Behat\WiremockExtension\Context;

use Behat\WiremockExtension\ServiceContainer\Wiremock;

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
