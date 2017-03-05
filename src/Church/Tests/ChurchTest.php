<?php

namespace Church\Tests;

use Church\Church;
use Church\DependencyInjection\Compiler\VerificationPass;
use Church\Utils\ArrayUtils;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ChurchTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Build Test.
     */
    public function testBuild()
    {
        $container = new ContainerBuilder();
        $church = new Church();
        $church->build($container);

        $passes = $container->getCompilerPassConfig()->getPasses();
        $pass = ArrayUtils::search($passes, function ($item) {
            return $item instanceof VerificationPass;
        });

        $this->assertInstanceOf(VerificationPass::class, $pass);
    }
}
