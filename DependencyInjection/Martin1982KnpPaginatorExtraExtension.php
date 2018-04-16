<?php declare(strict_types=1);

/**
 * KNP Paginator Extra Bundle
 * https://github.com/Martin1982/KnpPaginatorExtraBundle
 */
namespace Martin1982\KnpPaginatorExtraBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class Martin1982KnpPaginatorExtraExtension
 */
class Martin1982KnpPaginatorExtraExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $configs[] = 'paginate.yml';

        foreach ($configs as $config) {
            $loader->load($config);
        }
    }
}
