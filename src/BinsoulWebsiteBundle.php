<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\Website;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BinsoulWebsiteBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $ormCompilerClass = 'Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass';

        if (class_exists($ormCompilerClass)) {
            $container->addCompilerPass(
                DoctrineOrmMappingsPass::createAttributeMappingDriver(
                    ['BinSoul\Symfony\Bundle\Website'],
                    [realpath(__DIR__.'/src/Entity')],
                )
            );
        }
    }
}
