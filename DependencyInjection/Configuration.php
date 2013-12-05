<?php

namespace Kitpages\SemaphoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kitpages_semaphore');
        $rootNode
            ->children()
                ->scalarNode('sleep_time_microseconds')
                    ->isRequired()
                    ->defaultValue(100000)
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('dead_lock_microseconds')
                    ->isRequired()
                    ->defaultValue(5000000)
                    ->cannotBeEmpty()
            ->end();

        return $treeBuilder;
    }
}


