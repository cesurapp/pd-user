<?php

/**
 * This file is part of the pdAdmin pdWidget package.
 *
 * @package     pdWidget
 *
 * @author      Ramazan APAYDIN <iletisim@ramazanapaydin.com>
 * @copyright   Copyright (c) 2018 Ramazan APAYDIN
 * @license     LICENSE
 *
 * @link        https://github.com/rmznpydn/pd-widget
 */

namespace Pd\UserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class PdUserExtension extends Extension
{
    /**
     * Load Bundle Config and Services
     *
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // Load Configuration
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Set Configuration
        $container->setParameter('pd_user.user_class', $config['user_class']);
        $container->setParameter('pd_user.profile_class', $config['profile_class']);
        $container->setParameter('pd_user.group_class', $config['group_class']);
        $container->setParameter('pd_user.default_group', $config['default_group']);
        $container->setParameter('pd_user.login_redirect', $config['login_redirect']);
        $container->setParameter('pd_user.email_confirmation', $config['email_confirmation']);
        $container->setParameter('pd_user.welcome_email', $config['welcome_email']);

        // Load Services
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
    }
}
