<?php
/**
 * @author Vitalii Piskovyi <vitalii.piskovyi@gmail.com>
 */
namespace VPX\WiremockExtension\Context;

use Behat\Behat\Context\Context as ContextInterface;
use Behat\Behat\Definition\Call\Given;
use Behat\Behat\Hook\Call\BeforeScenario;
use Behat\Gherkin\Node\TableNode;
use VPX\WiremockExtension\Exception\WiremockException;

class WiremockContext extends WiremockAwareContext implements ContextInterface
{
    /**
     * @Given the following services exist with mappings:
     *
     * @param TableNode $tableNode
     * @throws WiremockException
     */
    public function theFollowingServicesExistWithMappings(TableNode $tableNode)
    {
        foreach ($tableNode->getHash() as $row) {
            if (!isset($row['service']) || !isset($row['mapping'])) {
                throw new WiremockException('You must provide a `service` and `mapping` column in your table node.');
            }

            $this->getWiremock()->addMappingForService($row['mapping'], $row['service']);
        }
    }

    /**
     * @Given :service exists with mapping :mapping
     *
     * @param string $service
     * @param string $mapping
     */
    public function serviceExistsWithWithMapping(string $service, string $mapping)
    {
        $this->getWiremock()->addMappingForService($mapping, $service);
    }

    /**
     * @BeforeScenario @wiremock-reset
     */
    public function resetWiremock()
    {
        $this->getWiremock()->resetMappings();
    }
}
