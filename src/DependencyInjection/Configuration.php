<?php

namespace ShelfUtilities\SelligentClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('selligent_client', 'array');
        $rootNode = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}
