<?php

namespace Room13\WizardBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class WizardCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if(!$container->hasDefinition('room13_wizard.manager'))
        {
            return;
        }

        $manager = $container->getDefinition('room13_wizard.manager');

        foreach($container->findTaggedServiceIds('room13.wizard') as $id=>$service)
        {
            $manager->addMethodCall('addWizard',array($id,new Reference($id)));
        }

    }


}
