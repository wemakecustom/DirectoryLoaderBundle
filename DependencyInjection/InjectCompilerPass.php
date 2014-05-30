<?php

namespace WMC\DirectoryLoaderBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Adds the routing directory at the beginning of the loaders to ensure
 * other loaders do not override it.
 *
 * The CompilerPass is added in `PassConfig::TYPE_BEFORE_REMOVING` so it runs after the others
 *
 * @see Symfony\Component\Routing\Loader\AnnotationDirectoryLoader
 */
class InjectCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('routing.resolver')) {
            return;
        }

        $definition = $container->getDefinition('routing.resolver');
        $calls = $definition->getMethodCalls();

        $call = array('addLoader', array(new Reference('wmc.directory_loader.routing_loader')));
        array_unshift($calls, $call);

        $definition->setMethodCalls($calls);
    }
}
