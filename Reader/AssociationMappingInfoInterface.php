<?php

namespace Ibrows\AttributeAssociationResolver\Reader;

use Ibrows\AttributeAssociationResolver\Attribute\AssociationInterface;

interface AssociationMappingInfoInterface
{
    /**
     * @return AssociationInterface
     */
    public function getAnnotation();

    /**
     * @return array
     */
    public function getMetaData();
}
