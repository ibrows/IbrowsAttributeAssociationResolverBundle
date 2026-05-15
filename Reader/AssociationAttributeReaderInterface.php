<?php

namespace Ibrows\AttributeAssociationResolver\Reader;

use Ibrows\AttributeReader\AttributeReaderInterface;
use Doctrine\ORM\EntityManager;

interface AssociationAttributeReaderInterface extends AttributeReaderInterface
{
    const
        ANNOTATION_TYPE_ASSOCIATION = 'AssociationInterface'
    ;

    /**
     * @param EntityManager $entityManager
     * @return AssociationAttributeReaderInterface
     */
    public function setEntityManager(EntityManager $entityManager);

    /**
     * @param string $className
     * @return AssociationMappingInfoInterface[]
     */
    public function getAssociationAnnotations($className);
}
