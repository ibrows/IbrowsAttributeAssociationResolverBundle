<?php

namespace Ibrows\AttributeAssociationResolver\Reader;

use Ibrows\AttributeReader\AttributeReader;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;

class AssociationAttributeReader extends AttributeReader implements AssociationAttributeReaderInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     * @return AssociationAttributeReader
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * @param string $className
     * @return AssociationMappingInfoInterface[]
     */
    public function getAssociationAnnotations($className)
    {
        $metaData = $this->getMetaData($className);
        $annotations = $this->getAnnotationsByType($className, self::ANNOTATION_TYPE_ASSOCIATION, self::SCOPE_PROPERTY);

        $associationAnnotations = array();

        foreach($annotations as $fieldName => $annotation){

            $associationMappings = $metaData->associationMappings[$fieldName];

            if(method_exists($annotation, 'getTargetEntity') && null !== $annotation->getTargetEntity()) {
                $associationMappings['targetEntity'] = $annotation->getTargetEntity();
            }

            $associationAnnotations[$fieldName] = new AssociationMappingInfo($annotation, $associationMappings);
        }

        return $associationAnnotations;
    }

    /**
     * @param $className
     * @return ClassMetadata
     */
    protected function getMetaData($className)
    {
        return $this->entityManager->getMetadataFactory()->getMetadataFor($className);
    }
}
