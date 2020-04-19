<?php

namespace SingleCategoryPlugin;

use Shopware\Components\Plugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Shopware-Plugin SingleCategoryPlugin.
 */
class SingleCategoryPlugin extends Plugin
{

    /**
    * @param ContainerBuilder $container
    */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('single_category_plugin.plugin_dir', $this->getPath());
        parent::build($container);
    }

}
