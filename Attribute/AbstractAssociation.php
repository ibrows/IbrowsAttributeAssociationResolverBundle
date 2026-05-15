<?php

namespace Ibrows\AttributeAssociationResolver\Attribute;

abstract class AbstractAssociation implements AssociationInterface
{
    public string $targetFieldName;
    public string $valueFieldName;
    public ?string $entitySetterName = null;
    public ?string $entityGetterName = null;
    public ?string $valueGetterName = null;
    public ?string $targetEntity = null;

    public function __construct(
        string $targetFieldName = '',
        string $valueFieldName = '',
        ?string $entitySetterName = null,
        ?string $entityGetterName = null,
        ?string $valueGetterName = null,
        ?string $targetEntity = null,
    ) {
        $this->targetFieldName = $targetFieldName;
        $this->valueFieldName = $valueFieldName;
        $this->entitySetterName = $entitySetterName;
        $this->entityGetterName = $entityGetterName;
        $this->valueGetterName = $valueGetterName;
        $this->targetEntity = $targetEntity;
    }

    /**
     * @return string
     */
    public function getTargetFieldName()
    {
        return $this->targetFieldName;
    }

    /**
     * @return string
     */
    public function getValueFieldName()
    {
        return $this->valueFieldName;
    }

    /**
     * @return string|null
     */
    public function getEntitySetterName()
    {
        return $this->entitySetterName;
    }

    /**
     * @return string|null
     */
    public function getEntityGetterName()
    {
        return $this->entityGetterName;
    }

    /**
     * @return string|null
     */
    public function getValueGetterName()
    {
        return $this->valueGetterName;
    }

    /**
     * @return string
     */
    public function getTargetEntity()
    {
        return $this->targetEntity;
    }

    /**
     * @return string
     */
    public function getType()
    {
        $explode = explode("\\", get_class($this));
        return end($explode);
    }
}
