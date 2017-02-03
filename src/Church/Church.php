<?php

namespace Church;

use Church\DependencyInjection\Compiler\VerificationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Church Bundle.
 */
class Church extends Bundle
{

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new VerificationPass());
    }
}
