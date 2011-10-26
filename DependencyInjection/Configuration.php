<?php

namespace Zim32\RequestLimitBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('zim32_request_limit');

		$rootNode
			->children()
				->arrayNode('rules')
					->isRequired()
					->requiresAtLeastOneElement()
					->prototype('array')
						->children()
							->scalarNode('path')->isRequired(true)->cannotBeEmpty(true)->end()
							->scalarNode('limit')->isRequired(true)->cannotBeEmpty(true)
								->validate()
									->ifTrue(function($v){return !is_numeric($v);})
									->thenInvalid("'Limit' must be numeric")
								->end()
							->end()
							->scalarNode('per')->isRequired(true)->cannotBeEmpty(true)
								->validate()
									->ifTrue(function($v){return !is_numeric($v);})
									->thenInvalid("'Per' must be numeric")
								->end()
							->end()
							->scalarNode('ip')->defaultNull()->end()
						->end()
					->end()
				->end()
			->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
