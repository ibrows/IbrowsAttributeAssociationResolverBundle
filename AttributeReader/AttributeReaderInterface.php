<?php

namespace Ibrows\AttributeReader;

interface AttributeReaderInterface
{
    const
        SCOPE_CLASS = 'class',
        SCOPE_METHOD = 'method',
        SCOPE_PROPERTY = 'property'
    ;

    /**
     * @param mixed $entity
     * @return array
     */
    public function getAnnotations($entity);

    /**
     * @param mixed $entity
     * @param string $type
     * @param string $scope
     * @return array
     */
    public function getAnnotationsByType($entity, $type, $scope);
}
