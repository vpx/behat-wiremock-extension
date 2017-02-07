<?php
/**
 * @author Vitalii Piskovyi <vitalii.piskovyi@gmail.com>
 */
namespace VPX\WiremockExtension\ServiceContainer;

use GuzzleHttp\ClientInterface;
use VPX\WiremockExtension\Exception\WiremockException;

class Wiremock
{
    const PATH_MAPPINGS_RESET = '/__admin/mappings/reset';
    const PATH_MAPPINGS = '/__admin/mappings';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $mappingPath;

    /**
     * @var array
     */
    private $preloadMappings;

    /**
     * @param ClientInterface $client
     * @param string $baseUrl
     * @param string $mappingPath
     * @param array $preloadMappings
     */
    public function __construct(ClientInterface $client, string $baseUrl, string $mappingPath, array $preloadMappings)
    {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
        $this->mappingPath = $mappingPath;
        $this->preloadMappings = $preloadMappings;
    }

    /**
     * @param array $mappings
     */
    public function loadMappings(array $mappings)
    {
        foreach ($mappings as $mapping) {
            if (!isset($mapping['service']) || !isset($mapping['mapping'])) {
                throw new WiremockException(
                    'You must provide a `service` and `mapping` column in your table node.',
                    500
                );
            }

            $this->addMappingForService($mapping['mapping'], $mapping['service']);
        }
    }

    /**
     * @param string $mapping
     * @param string $service
     */
    public function addMappingForService(string $mapping, string $service)
    {
        $response = $this->client->request(
            'POST',
            $this->baseUrl . self::PATH_MAPPINGS,
            [
                'body'    => $this->getMappingContent($service, $mapping),
                'headers' => $this->getHeaders(),
            ]
        );

        if (201 !== $response->getStatusCode()) {
            throw new WiremockException(
                sprintf('Wiremock\'s mapping was not added. The reason is: %s', $response->getBody()),
                $response->getStatusCode()
            );
        }
    }

    /**
     * @throws WiremockException
     */
    public function resetMappings()
    {
        $response = $this->client->request(
            'POST',
            $this->baseUrl . self::PATH_MAPPINGS_RESET,
            ['headers' => $this->getHeaders()]
        );

        if (200 !== $response->getStatusCode()) {
            throw new WiremockException(
                sprintf('Wiremock resetting was not completed. The response is: %s', $response->getBody()),
                $response->getStatusCode()
            );
        }

        $this->loadMappings($this->preloadMappings);
    }

    /**
     * @return array
     */
    private function getHeaders(): array
    {
        return ['content_type' => 'application/json'];
    }

    /**
     * @param string $service
     * @param string $mapping
     *
     * @return string
     * @throws WiremockException
     */
    private function getMappingContent(string $service, string $mapping): string
    {
        $path = sprintf('%s/%s/%s', rtrim($this->mappingPath, '/'), $service, ltrim($mapping, '/'));

        if (!is_file($path)) {
            throw new WiremockException(sprintf('Mapping file `%s` does not exist.', $path), 404);
        }

        $content = file_get_contents($path);

        if (empty($content)) {
            throw new WiremockException(sprintf('Mapping file `%s` is empty.', $path), 409);
        }

        return $content;
    }
}
