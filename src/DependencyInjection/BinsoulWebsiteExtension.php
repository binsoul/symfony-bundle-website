<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\Website\DependencyInjection;

use BinSoul\Symfony\Bundle\Website\EventListener\TablePrefixListener;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Extension\Extension;

class BinsoulWebsiteExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $prefix = trim($config['prefix'] ?? '');

        $definition = $container->getDefinition(TablePrefixListener::class);
        $definition->setArgument(0, $prefix);

        if ($prefix === '') {
            $definition->clearTags();
        }
    }
}
