<?php

namespace SingleCategoryPlugin\Tests;

use SingleCategoryPlugin\SingleCategoryPlugin as Plugin;
use Shopware\Components\Test\Plugin\TestCase;

class PluginTest extends TestCase
{
    protected static $ensureLoadedPlugins = [
        'SingleCategoryPlugin' => []
    ];

    public function testCanCreateInstance()
    {
        /** @var Plugin $plugin */
        $plugin = Shopware()->Container()->get('kernel')->getPlugins()['SingleCategoryPlugin'];

        $this->assertInstanceOf(Plugin::class, $plugin);
    }
}
