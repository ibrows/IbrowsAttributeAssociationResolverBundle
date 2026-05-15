<?php

namespace Ibrows\AttributeAssociationResolver\Resolver;

use Ibrows\AttributeAssociationResolver\Resolver\Type\ResolverInterface as ResolverTypeInterface;

interface ResolverChainInterface extends ResolverTypeInterface
{
    /**
     * @param ResolverTypeInterface $resolver
     * @return ResolverChainInterface
     */
    public function addResolver(ResolverTypeInterface $resolver);

    /**
     * @param ResolverTypeInterface $resolver
     * @return ResolverChainInterface
     */
    public function removeResolver(ResolverTypeInterface $resolver);

    /**
     * @return ResolverTypeInterface[]
     */
    public function getResolvers();
}
