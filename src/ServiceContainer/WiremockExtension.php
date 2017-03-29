<?php
/**
 * @author Vitalii Piskovyi <vitalii.piskovyi@gmail.com>
 */
namespace VPX\WiremockExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use GuzzleHttp\Client;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use VPX\WiremockExtension\Context\Initializer\WiremockAwareInitializer;

class WiremockExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'wiremock';
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('base_url')
                    ->defaultValue('http://localhost:8080')
                ->end()
                ->scalarNode('mapping_path')
                    ->isRequired()
                ->end()
                ->arrayNode('preload_mappings')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('service')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('mapping')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $container->setParameter('wiremock.base_url', $config['base_url']);
        $container->setParameter('wiremock.mapping_path', $config['mapping_path']);
        $container->setParameter('wiremock.preload_mappings', $config['preload_mappings']);

        $container->setDefinition('wiremock.guzzle_client', new Definition(Client::class));

        $definition = new Definition(
            Wiremock::class,
            [
                new Reference('wiremock.guzzle_client'),
                $container->getParameter('wiremock.base_url'),
                $container->getParameter('wiremock.mapping_path'),
                $container->getParameter('wiremock.preload_mappings')
            ]
        );
        $container->setDefinition('wiremock.client', $definition);

        $definition = new Definition(WiremockAwareInitializer::class, [new Reference('wiremock.client')]);
        $definition->addTag(ContextExtension::INITIALIZER_TAG, ['priority' => 0]);
        $container->setDefinition('wiremock.context_initializer', $definition);
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
    }
}
