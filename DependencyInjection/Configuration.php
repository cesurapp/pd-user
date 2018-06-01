<?php

/**
 * This file is part of the pdAdmin pdUser package.
 *
 * @package     pdUser
 *
 * @author      Ramazan APAYDIN <iletisim@ramazanapaydin.com>
 * @copyright   Copyright (c) 2018 Ramazan APAYDIN
 * @license     LICENSE
 *
 * @link        https://github.com/rmznpydn/pd-user
 */

namespace Pd\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('pd_user');

        // Set Configuration
        $rootNode
            ->children()
                ->scalarNode('user_class')->isRequired()->end()
                ->scalarNode('profile_class')->isRequired()->end()
                ->scalarNode('group_class')->isRequired()->end()
                ->scalarNode('default_group')->end()
                ->scalarNode('login_redirect')->cannotBeEmpty()->end()
                ->booleanNode('email_confirmation')->defaultFalse()->end()
                ->booleanNode('welcome_email')->defaultTrue()->end()
                ->scalarNode('template_path')->defaultValue('@PdUser')->end()
                ->integerNode('resetting_request_time')->defaultValue(7200)->end()
            ->end();

        return $treeBuilder;
    }
}
