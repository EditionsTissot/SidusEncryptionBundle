<?php
/*
 * This file is part of the Sidus/EncryptionBundle package.
 *
 * Copyright (c) 2015-2018 Vincent Chalnot
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sidus\EncryptionBundle\DependencyInjection;

use Sidus\EncryptionBundle\Doctrine\Type\EncryptStringType;
use Sidus\EncryptionBundle\Doctrine\Type\EncryptTextType;
use Sidus\EncryptionBundle\Registry\EncryptionManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
class SidusEncryptionExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/services'));
        $loader->load('encryption.yaml');
        $loader->load('doctrine.yaml');
        $loader->load('registry.yaml');
        $loader->load('security.yaml');
        $loader->load('session.yaml');
        
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $registry = $container->getDefinition(EncryptionManagerRegistry::class);
        $registry->replaceArgument('$defaultCode', $config['preferred_adapter']);
        $container->setParameter('sidus.encryption.throw_exceptions', $config['throw_exception']);
    }
    
    public function prepend(ContainerBuilder $container)
    {
        $doctrineConfiguration = $container->getExtensionConfig('doctrine')[0];
        
        if (empty($doctrineConfiguration['dbal'])) {
            $doctrineConfiguration['dbal'] = [];
        }
        
        if (empty($doctrineConfiguration['dbal']['types'])) {
            $doctrineConfiguration['dbal']['types'] = [];
        }
        $doctrineConfiguration['dbal']['types'] += [
            'encrypt_string' => EncryptStringType::class,
            'encrypt_text' => EncryptTextType::class
        ];
        $container->prependExtensionConfig('doctrine', $doctrineConfiguration);
    }
}
