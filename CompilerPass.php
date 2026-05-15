<?php

namespace Ibrows\AttributeAssociationResolver;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class CompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if(!$container->hasDefinition('ibrows_attributeassociationresolver.resolverchain')) {
            return;
        }

        $resolverChain = $container->getDefinition(
            'ibrows_attributeassociationresolver.resolverchain'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'ibrows_attributeassociationresolver.resolverchain'
        );

        uasort($taggedServices, function($a, $b) {
            $a = isset($a[0]['priority']) ? $a[0]['priority'] : 0;
            $b = isset($b[0]['priority']) ? $b[0]['priority'] : 0;
            return $a > $b ? -1 : 1;
        });

        foreach($taggedServices as $id => $attributes){
            $resolverChain->addMethodCall(
                'addResolver',
                array(new Reference($id))
            );
        }
    }
}
