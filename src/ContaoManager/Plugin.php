<?php
/**
 *  Copyright Information
 *
 * @copyright: 2018 agentur fipps e.K.
 * @author   : Arne Borchert
 * @license  : LGPL 3.0+
 */

namespace Fipps\FileinfoBundle\ContaoManager;

use Fipps\FileinfoBundle\FippsFileinfoBundle;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;

/**
 * Plugin for the Contao Manager.
 */
class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(FippsFileinfoBundle::class)->setLoadAfter(
                [
                    ContaoCoreBundle::class,
                    'filecredits'
                ]
            )
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader, array $config)
    {
        $file = '@FippsFileinfoBundle/Resources/config/config.yml';
        
        $loader->load($file);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel)
    {
        $file = '@FippsFileinfoBundle/Resources/config/routing.yml';

        return $resolver->resolve($file)->load($file);
    }

    /**
     * {@inheritdoc}
     */
    public function getPackageDependencies()
    {
        return ['heimrichhannot/contao-filecredits'];
    }
}