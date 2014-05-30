<?php

namespace WMC\DirectoryLoaderBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;

use WMC\DirectoryLoaderBundle\DependencyInjection\InjectCompilerPass;

class WMCDirectoryLoaderBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new InjectCompilerPass(), PassConfig::TYPE_BEFORE_REMOVING);
    }
}
