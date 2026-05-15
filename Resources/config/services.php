<?php

use Ibrows\AttributeAssociationResolver\Reader\AssociationAttributeReader;
use Ibrows\AttributeAssociationResolver\Resolver\Resolver;
use Ibrows\AttributeAssociationResolver\Resolver\ResolverChain;
use Ibrows\AttributeAssociationResolver\Resolver\Type\ManyToMany;
use Ibrows\AttributeAssociationResolver\Resolver\Type\ManyToOne;
use Ibrows\AttributeAssociationResolver\Resolver\Type\OneToMany;
use Ibrows\AttributeAssociationResolver\Resolver\Type\OneToOne;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $container->parameters()
        ->set('ibrows_attributeassociationresolver.softdelete', true)
        ->set('ibrows_attributeassociationresolver.softdeletegetter', 'getDeletedAt');

    $services = $container->services();

    $services->set('ibrows_attributeassociationresolver.resolver', Resolver::class)
        ->call('setAnnotationReader', [service('ibrows_attributeassociationresolver.attribute.reader')])
        ->call('setEntityManager', [service('doctrine.orm.entity_manager')])
        ->call('setResolverChain', [service('ibrows_attributeassociationresolver.resolverchain')]);

    $services->set('ibrows_attributeassociationresolver.resolverchain', ResolverChain::class);

    $services->set('ibrows_attributeassociationresolver.attribute.reader', AssociationAttributeReader::class)
        ->call('setEntityManager', [service('doctrine.orm.entity_manager')]);

    foreach ([
        'ibrows_attributeassociationresolver.resolver.manytomany' => ManyToMany::class,
        'ibrows_attributeassociationresolver.resolver.manytoone'  => ManyToOne::class,
        'ibrows_attributeassociationresolver.resolver.onetoone'   => OneToOne::class,
        'ibrows_attributeassociationresolver.resolver.onetomany'  => OneToMany::class,
    ] as $id => $class) {
        $services->set($id, $class)
            ->arg(0, service('doctrine.orm.entity_manager'))
            ->tag('ibrows_attributeassociationresolver.resolverchain', ['priority' => -20])
            ->call('setSoftdeletable', ['%ibrows_attributeassociationresolver.softdelete%'])
            ->call('setSoftdeletableGetter', ['%ibrows_attributeassociationresolver.softdeletegetter%']);
    }
};
