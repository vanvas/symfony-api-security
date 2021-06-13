<?php
declare(strict_types=1);

namespace Vim\ApiSecurity\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('api_security');

        $treeBuilder
            ->getRootNode()
            ->children()
            ->scalarNode('jwt_login_route_name')->end()
            ->arrayNode('jwt_config')
            ->children()
            ->scalarNode('secret_key')->end()
            ->integerNode('leeway')->end()
            ->scalarNode('exp')->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
