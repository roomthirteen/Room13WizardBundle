<?php

namespace Room13\WizardBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Room13\WizardBundle\DependencyInjection\Compiler\WizardCompilerPass;

class Room13WizardBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new WizardCompilerPass());
    }

}
