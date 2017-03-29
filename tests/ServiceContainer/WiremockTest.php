<?php
/**
 * @author Vitalii Piskovyi <vitalii.piskovyi@gmail.com>
 */
namespace Tests\VPX\ServiceContainer;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use VPX\WiremockExtension\Exception\WiremockException;
use VPX\WiremockExtension\ServiceContainer\Wiremock;

class WiremockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClientInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $guzzleClientMock;

    /**
     * @var ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $responseMock;

    /**
     * @var ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resetResponseMock;

    /**
     * @var Wiremock
     */
    private $wiremock;

    /**
     * @dataProvider mappingDataProvider
     *
     * @param array $mappings
     */
    public function testLoadMappings(array $mappings)
    {
        $this->mockAddMappingWithFile($this->at(0), '/foo/bar.json');
        $this->mockResponseStatusCode(201);

        $this->wiremock->loadMappings($mappings);
    }

    /**
     * @dataProvider mappingDataProvider
     *
     * @param array $mappings
     */
    public function testAddMappingWithFailureWiremockResponse(array $mappings)
    {
        $this->mockAddMappingWithFile($this->at(0), '/foo/bar.json');
        $this->mockResponseStatusCode(400);
        $this->mockResponseBody('foo');

        $this->expectException(WiremockException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Wiremock\'s mapping was not added. The reason is: foo');

        $this->wiremock->loadMappings($mappings);
    }

    public function testAddMappingWithWrongParameters()
    {
        $this->expectException(WiremockException::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessage('You must provide a `service` and `mapping` column in your table node.');

        $this->wiremock->loadMappings(['foo', 'bar']);
    }

    public function testAddMappingWithWrongFile()
    {
        $this->expectException(WiremockException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage(
            sprintf('Mapping file `%s` does not exist.', $this->getMappingsPath() . '/bar/foo')
        );

        $this->wiremock->addMappingForService('foo', 'bar');
    }

    public function testAddMappingWithEmptyContent()
    {
        $this->expectException(WiremockException::class);
        $this->expectExceptionCode(409);
        $this->expectExceptionMessage(sprintf('Mapping file `%s` is empty.', $this->getMappingsPath() . '/foo/empty.json'));

        $this->wiremock->addMappingForService('empty.json', 'foo');
    }

    public function testResetMappings()
    {
        $this->mockResetAction($this->at(0));
        $this->mockResetResponseStatusCode(200);

        $this->mockAddMappingWithFile($this->at(1), '/baz/qux.json');
        $this->mockResponseStatusCode(201);

        $this->wiremock->resetMappings();
    }

    public function testResetMappingsWithWrongWiremockResponse()
    {
        $this->mockResetAction($this->at(0));
        $this->mockResetResponseStatusCode(500);
        $this->mockResetResponseBody('foo');

        $this->expectException(WiremockException::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessage('Wiremock resetting was not completed. The response is: foo');


        $this->wiremock->resetMappings();
    }

    /**
     * @return array
     */
    public function mappingDataProvider(): array
    {
        return [
            [
                [
                    [
                        'service' => 'foo',
                        'mapping' => 'bar.json',
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->guzzleClientMock = $this->getMockBuilder(ClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->responseMock = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resetResponseMock = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();


        $this->wiremock = new Wiremock(
            $this->guzzleClientMock,
            'foo',
            $this->getMappingsPath(),
            [
                [
                    'service' => 'baz',
                    'mapping' => 'qux.json',
                ],
            ]
        );
    }

    /**
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $expectation
     * @param string $path
     */
    private function mockAddMappingWithFile(\PHPUnit_Framework_MockObject_Matcher_Invocation $expectation, string $path)
    {
        $this->guzzleClientMock
            ->expects($expectation)
            ->method('request')
            ->with(
                'POST',
                'foo/__admin/mappings',
                [
                    'body'    => file_get_contents($this->getMappingsPath() . $path),
                    'headers' => [
                        'content_type' => 'application/json',
                    ],
                ]
            )
            ->willReturn($this->responseMock);
    }

    /**
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $expectation
     */
    private function mockResetAction(\PHPUnit_Framework_MockObject_Matcher_Invocation $expectation)
    {
        $this->guzzleClientMock
            ->expects($expectation)
            ->method('request')
            ->with(
                'POST',
                'foo/__admin/mappings/reset',
                [
                    'headers' => [
                        'content_type' => 'application/json',
                    ],
                ]
            )
            ->willReturn($this->resetResponseMock);
    }

    /**
     * @param int $statusCode
     */
    private function mockResponseStatusCode(int $statusCode)
    {
        $this->responseMock
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn($statusCode);
    }

    /**
     * @param int $statusCode
     */
    private function mockResetResponseStatusCode(int $statusCode)
    {
        $this->resetResponseMock
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn($statusCode);
    }

    /**
     * @param string $body
     */
    private function mockResponseBody(string $body)
    {
        $this->responseMock
            ->expects($this->any())
            ->method('getBody')
            ->willReturn($body);
    }

    /**
     * @param string $body
     */
    private function mockResetResponseBody(string $body)
    {
        $this->resetResponseMock
            ->expects($this->any())
            ->method('getBody')
            ->willReturn($body);
    }

    /**
     * @return string
     */
    private function getMappingsPath(): string
    {
        return __DIR__ . '/../Resources/fixtures';
    }
}
