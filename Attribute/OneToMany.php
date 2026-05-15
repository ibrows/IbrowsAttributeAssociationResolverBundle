<?php

namespace Ibrows\AttributeAssociationResolver\Attribute;

#[\Attribute]
class OneToMany extends AbstractAssociation
{
    public ?string $collectionAddFunctionName = null;
    public ?string $collectionRemoveFunctionName = null;

    public function __construct(
        string $targetFieldName = '',
        string $valueFieldName = '',
        ?string $entitySetterName = null,
        ?string $entityGetterName = null,
        ?string $valueGetterName = null,
        ?string $targetEntity = null,
        ?string $collectionAddFunctionName = null,
        ?string $collectionRemoveFunctionName = null,
    ) {
        parent::__construct($targetFieldName, $valueFieldName, $entitySetterName, $entityGetterName, $valueGetterName, $targetEntity);
        $this->collectionAddFunctionName = $collectionAddFunctionName;
        $this->collectionRemoveFunctionName = $collectionRemoveFunctionName;
    }

    /**
     * @param mixed $collectionAddFunctionName
     */
    public function setCollectionAddFunctionName($collectionAddFunctionName)
    {
        $this->collectionAddFunctionName = $collectionAddFunctionName;
    }

    /**
     * @return mixed
     */
    public function getCollectionAddFunctionName()
    {
        return $this->collectionAddFunctionName;
    }

    /**
     * @param mixed $collectionRemoveFunctionName
     */
    public function setCollectionRemoveFunctionName($collectionRemoveFunctionName)
    {
        $this->collectionRemoveFunctionName = $collectionRemoveFunctionName;
    }

    /**
     * @return mixed
     */
    public function getCollectionRemoveFunctionName()
    {
        return $this->collectionRemoveFunctionName;
    }
}
